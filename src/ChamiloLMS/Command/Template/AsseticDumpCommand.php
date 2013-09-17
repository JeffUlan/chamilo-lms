<?php

namespace ChamiloLMS\Command\Template;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;

/**
 * Class AsseticDumpCommand
 * @package ChamiloLMS\Command\Template
 */
class AsseticDumpCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('assetic:dump')
            ->setDescription('Dumps all assets to the filesystem')
            ->addArgument('theme', InputArgument::OPTIONAL, 'The theme to dump, if none is set then all themes will be generated', null);
    }

    /**
     * @param Console\Input\InputInterface $input
     * @param Console\Output\OutputInterface $output
     * @return integer|null|boolean|void
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        /** @var \Silex\Application $app */
        $app = $this->getApplication()->getSilexApplication();

        $theme = $input->getArgument('theme');

        // Get all themes
        if ($app['assetic.enabled'] == false || !isset($app['assetic.enabled'])) {
            $output->writeln("<info>Assetic is not enabled. Change this value to true:</info> <comment>assetic.enabled = true</comment>");
            return false;
        }

        $themes = $app['template']->getStyleSheetFolderList();
        //$themes = array('chamilo', 'public_admin');

        if (empty($theme)) {
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation(
                    $output,
                    '<question>Are you sure you want to dump css/js of all themes?</question>(y/N)',
                    false
                )
            ) {
                return;
            }
        } else {
            $themes = array($theme);
        }

        if (!is_dir($app['assetic.path_to_web'])) {
            mkdir($app['assetic.path_to_web'], api_get_permissions_for_new_directories());
            $output->writeln("<info>Creating folder in: </info><comment>".$app['assetic.path_to_web']."</comment>");
        }

        foreach ($themes as $theme) {
            if (is_dir($app['root_sys'].'main/css/'.$theme) && file_exists($app['root_sys'].'main/css/'.$theme.'/default.css')) {
                $output->writeln("<info>Dumping theme: $theme</info>");

                /** @var \SilexAssetic\Assetic\Dumper $dumper */
                $dumper = $app['assetic.dumper'];

                $app['assetic.output.path_to_css'] = 'css/'.$theme.'/style.css';

                $params = array(
                    $app['root_sys'].'main/css/'.$theme => $app['assetic.input.path_to_assets'].'/css/'.$theme
                );

                $app['chamilo.filesystem']->copyFolders($params, $output);

                if (isset($app['twig'])) {
                    //$dumper->addTwigAssets();
                }
                $dumper->dumpAssets();
            } else {
                $output->writeln("<info>Seems that theme: <comment>$theme</comment> doesn't have a <comment>$theme/default.css</comment> file. Skipping dump.</info>");
            }
        }
        $output->writeln('<info>Dump finished</info>');

        return true;
    }
}
