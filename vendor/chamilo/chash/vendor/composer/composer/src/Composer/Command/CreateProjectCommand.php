<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Command;

use Composer\Config;
use Composer\Factory;
use Composer\Installer;
use Composer\Installer\ProjectInstaller;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\Repository\ComposerRepository;
use Composer\Repository\CompositeRepository;
use Composer\Repository\FilesystemRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Composer\Script\ScriptEvents;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Composer\Json\JsonFile;
use Composer\Config\JsonConfigSource;
use Composer\Util\Filesystem;
use Composer\Util\RemoteFilesystem;
use Composer\Package\Version\VersionParser;

/**
 * Install a package as new project into new directory.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Tobias Munk <schmunk@usrbin.de>
 */
class CreateProjectCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('create-project')
            ->setDescription('Create new project from a package into given directory.')
            ->setDefinition(array(
                new InputArgument('package', InputArgument::OPTIONAL, 'Package name to be installed'),
                new InputArgument('directory', InputArgument::OPTIONAL, 'Directory where the files should be created'),
                new InputArgument('version', InputArgument::OPTIONAL, 'Version, will defaults to latest'),
                new InputOption('stability', 's', InputOption::VALUE_REQUIRED, 'Minimum-stability allowed (unless a version is specified).', 'stable'),
                new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
                new InputOption('prefer-dist', null, InputOption::VALUE_NONE, 'Forces installation from package dist even for dev versions.'),
                new InputOption('repository-url', null, InputOption::VALUE_REQUIRED, 'Pick a different repository url to look for the package.'),
                new InputOption('dev', null, InputOption::VALUE_NONE, 'Enables installation of require-dev packages (enabled by default, only present for BC).'),
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables installation of require-dev packages.'),
                new InputOption('no-custom-installers', null, InputOption::VALUE_NONE, 'Whether to disable custom installers.'),
                new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Whether to prevent execution of all defined scripts in the root package.'),
                new InputOption('no-progress', null, InputOption::VALUE_NONE, 'Do not output download progress.'),
                new InputOption('keep-vcs', null, InputOption::VALUE_NONE, 'Whether to prevent deletion vcs folder.'),
            ))
            ->setHelp(<<<EOT
The <info>create-project</info> command creates a new project from a given
package into a new directory. If executed without params and in a directory
with a composer.json file it installs the packages for the current project.

You can use this command to bootstrap new projects or setup a clean
version-controlled installation for developers of your project.

<info>php composer.phar create-project vendor/project target-directory [version]</info>

You can also specify the version with the package name using = or : as separator.

To install unstable packages, either specify the version you want, or use the
--stability=dev (where dev can be one of RC, beta, alpha or dev).

To setup a developer workable version you should create the project using the source
controlled code by appending the <info>'--prefer-source'</info> flag.

To install a package from another repository than the default one you
can pass the <info>'--repository-url=http://myrepository.org'</info> flag.

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = Factory::createConfig();

        $preferSource = false;
        $preferDist = false;
        switch ($config->get('preferred-install')) {
            case 'source':
                $preferSource = true;
                break;
            case 'dist':
                $preferDist = true;
                break;
            case 'auto':
            default:
                // noop
                break;
        }
        if ($input->getOption('prefer-source') || $input->getOption('prefer-dist')) {
            $preferSource = $input->getOption('prefer-source');
            $preferDist = $input->getOption('prefer-dist');
        }

        return $this->installProject(
            $this->getIO(),
            $config,
            $input->getArgument('package'),
            $input->getArgument('directory'),
            $input->getArgument('version'),
            $input->getOption('stability'),
            $preferSource,
            $preferDist,
            !$input->getOption('no-dev'),
            $input->getOption('repository-url'),
            $input->getOption('no-custom-installers'),
            $input->getOption('no-scripts'),
            $input->getOption('keep-vcs'),
            $input->getOption('no-progress')
        );
    }

    public function installProject(IOInterface $io, $config, $packageName, $directory = null, $packageVersion = null, $stability = 'stable', $preferSource = false, $preferDist = false, $installDevPackages = false, $repositoryUrl = null, $disableCustomInstallers = false, $noScripts = false, $keepVcs = false, $noProgress = false)
    {
        $oldCwd = getcwd();

        if ($packageName !== null) {
            $installedFromVcs = $this->installRootPackage($io, $config, $packageName, $directory, $packageVersion, $stability, $preferSource, $preferDist, $installDevPackages, $repositoryUrl, $disableCustomInstallers, $noScripts, $keepVcs, $noProgress);
        } else {
            $installedFromVcs = false;
        }

        $composer = Factory::create($io);

        if ($noScripts === false) {
            // dispatch event
            $composer->getEventDispatcher()->dispatchCommandEvent(ScriptEvents::POST_ROOT_PACKAGE_INSTALL, $installDevPackages);
        }

        // install dependencies of the created project
        $installer = Installer::create($io, $composer);
        $installer->setPreferSource($preferSource)
            ->setPreferDist($preferDist)
            ->setDevMode($installDevPackages)
            ->setRunScripts( ! $noScripts);

        if ($disableCustomInstallers) {
            $installer->disableCustomInstallers();
        }

        if (!$installer->run()) {
            return 1;
        }

        $hasVcs = $installedFromVcs;
        if (!$keepVcs && $installedFromVcs
            && (
                !$io->isInteractive()
                || $io->askConfirmation('<info>Do you want to remove the existing VCS (.git, .svn..) history?</info> [<comment>Y,n</comment>]? ', true)
            )
        ) {
            $finder = new Finder();
            $finder->depth(0)->directories()->in(getcwd())->ignoreVCS(false)->ignoreDotFiles(false);
            foreach (array('.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg') as $vcsName) {
                $finder->name($vcsName);
            }

            try {
                $fs = new Filesystem();
                $dirs = iterator_to_array($finder);
                unset($finder);
                foreach ($dirs as $dir) {
                    if (!$fs->removeDirectory($dir)) {
                        throw new \RuntimeException('Could not remove '.$dir);
                    }
                }
            } catch (\Exception $e) {
                $io->write('<error>An error occurred while removing the VCS metadata: '.$e->getMessage().'</error>');
            }

            $hasVcs = false;
        }

        // rewriting self.version dependencies with explicit version numbers if the package's vcs metadata is gone
        if (!$hasVcs) {
            $package = $composer->getPackage();
            $configSource = new JsonConfigSource(new JsonFile('composer.json'));
            foreach (BasePackage::$supportedLinkTypes as $type => $meta) {
                foreach ($package->{'get'.$meta['method']}() as $link) {
                    if ($link->getPrettyConstraint() === 'self.version') {
                        $configSource->addLink($type, $link->getTarget(), $package->getPrettyVersion());
                    }
                }
            }
        }

        if ($noScripts === false) {
            // dispatch event
            $composer->getEventDispatcher()->dispatchCommandEvent(ScriptEvents::POST_CREATE_PROJECT_CMD, $installDevPackages);
        }

        chdir($oldCwd);
        $vendorComposerDir = $composer->getConfig()->get('vendor-dir').'/composer';
        if (is_dir($vendorComposerDir) && glob($vendorComposerDir.'/*') === array() && count(glob($vendorComposerDir.'/.*')) === 2) {
            @rmdir($vendorComposerDir);
            $vendorDir = $composer->getConfig()->get('vendor-dir');
            if (is_dir($vendorDir) && glob($vendorDir.'/*') === array() && count(glob($vendorDir.'/.*')) === 2) {
                @rmdir($vendorDir);
            }
        }

        return 0;
    }

    protected function installRootPackage(IOInterface $io, $config, $packageName, $directory = null, $packageVersion = null, $stability = 'stable', $preferSource = false, $preferDist = false, $installDevPackages = false, $repositoryUrl = null, $disableCustomInstallers = false, $noScripts = false, $keepVcs = false, $noProgress = false)
    {
        $stability = strtolower($stability);
        if ($stability === 'rc') {
            $stability = 'RC';
        }
        if (!isset(BasePackage::$stabilities[$stability])) {
            throw new \InvalidArgumentException('Invalid stability provided ('.$stability.'), must be one of: '.implode(', ', array_keys(BasePackage::$stabilities)));
        }

        if (null === $repositoryUrl) {
            $sourceRepo = new CompositeRepository(Factory::createDefaultRepositories($io, $config));
        } elseif ("json" === pathinfo($repositoryUrl, PATHINFO_EXTENSION)) {
            $sourceRepo = new FilesystemRepository(new JsonFile($repositoryUrl, new RemoteFilesystem($io)));
        } elseif (0 === strpos($repositoryUrl, 'http')) {
            $sourceRepo = new ComposerRepository(array('url' => $repositoryUrl), $io, $config);
        } else {
            throw new \InvalidArgumentException("Invalid repository url given. Has to be a .json file or an http url.");
        }

        $parser = new VersionParser();
        $candidates = array();
        $requirements = $parser->parseNameVersionPairs(array($packageName));
        $name = strtolower($requirements[0]['name']);
        if (!$packageVersion && isset($requirements[0]['version'])) {
            $packageVersion = $requirements[0]['version'];
        }

        $pool = new Pool($packageVersion ? 'dev' : $stability);
        $pool->addRepository($sourceRepo);

        $constraint = $packageVersion ? new VersionConstraint('=', $parser->normalize($packageVersion)) : null;
        $candidates = $pool->whatProvides($name, $constraint);
        foreach ($candidates as $key => $candidate) {
            if ($candidate->getName() !== $name) {
                unset($candidates[$key]);
            }
        }

        if (!$candidates) {
            throw new \InvalidArgumentException("Could not find package $name" . ($packageVersion ? " with version $packageVersion." : " with stability $stability."));
        }

        if (null === $directory) {
            $parts = explode("/", $name, 2);
            $directory = getcwd() . DIRECTORY_SEPARATOR . array_pop($parts);
        }

        // select highest version if we have many
        $package = reset($candidates);
        foreach ($candidates as $candidate) {
            if (version_compare($package->getVersion(), $candidate->getVersion(), '<')) {
                $package = $candidate;
            }
        }
        unset($candidates);

        $io->write('<info>Installing ' . $package->getName() . ' (' . VersionParser::formatVersion($package, false) . ')</info>');

        if ($disableCustomInstallers) {
            $io->write('<info>Custom installers have been disabled.</info>');
        }

        if (0 === strpos($package->getPrettyVersion(), 'dev-') && in_array($package->getSourceType(), array('git', 'hg'))) {
            $package->setSourceReference(substr($package->getPrettyVersion(), 4));
        }

        $dm = $this->createDownloadManager($io, $config);
        $dm->setPreferSource($preferSource)
            ->setPreferDist($preferDist)
            ->setOutputProgress(!$noProgress);

        $projectInstaller = new ProjectInstaller($directory, $dm);
        $im = $this->createInstallationManager();
        $im->addInstaller($projectInstaller);
        $im->install(new InstalledFilesystemRepository(new JsonFile('php://memory')), new InstallOperation($package));
        $im->notifyInstalls();

        $installedFromVcs = 'source' === $package->getInstallationSource();

        $io->write('<info>Created project in ' . $directory . '</info>');
        chdir($directory);

        putenv('COMPOSER_ROOT_VERSION='.$package->getPrettyVersion());

        return $installedFromVcs;
    }

    protected function createDownloadManager(IOInterface $io, Config $config)
    {
        $factory = new Factory();

        return $factory->createDownloadManager($io, $config);
    }

    protected function createInstallationManager()
    {
        return new InstallationManager();
    }
}
