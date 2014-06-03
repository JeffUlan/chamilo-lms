<?php

/**
 * This file is part of the DigitalOcean library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalOcean\CLI\Events;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use DigitalOcean\CLI\Command;

/**
 * Command-line events:show class.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ShowCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('events:show')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the event')
            ->setDescription('Reports on the progress of an event')
            ->addOption('credentials', null, InputOption::VALUE_REQUIRED,
                'If set, the yaml file which contains your credentials', Command::DEFAULT_CREDENTIALS_FILE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $digitalOcean = $this->getDigitalOcean($input->getOption('credentials'));
        $event        = $digitalOcean->events()->show($input->getArgument('id'))->event;

        $content   = array();
        $content[] = array(
            $event->id,
            $event->action_status,
            $event->droplet_id,
            $event->event_type_id,
            $event->percentage,
        );
        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array(
                    'ID',
                    'Status',
                    'Droplet ID',
                    'Event Type ID',
                    'Percentage',
                ))
            ->setRows($content);

        $table->render($output);
    }
}
