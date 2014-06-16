<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\TwigBundle\Command;

use Symfony\Bridge\Twig\Command\LintCommand as BaseLintCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Finder\Finder;

/**
 * Command that will validate your template syntax and output encountered errors.
 *
 * @author Marc Weistroff <marc.weistroff@sensiolabs.com>
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class LintCommand extends BaseLintCommand implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTwigEnvironment()
    {
        return $this->container->get('twig');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setHelp(
                $this->getHelp().<<<EOF


Or all template files in a bundle:

<info>php %command.full_name% @AcmeDemoBundle</info>
EOF
            )
        ;
    }

    protected function findFiles($filename)
    {
        if (0 === strpos($filename, '@')) {
            $dir = $this->getApplication()->getKernel()->locateResource($filename);

            return Finder::create()->files()->in($dir)->name('*.twig');
        }

        return parent::findFiles($filename);
    }
}
