<?php
/**
 * Definition of command to
 * enable a language
 * Does not support multi-url yet
 */
/**
 * Necessary namespaces definitions and usage
 */
namespace Chash\Command\Translation;

use Chash\Command\Database\CommonChamiloDatabaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Definition of the translation:enable command
 */
class EnableLanguageCommand extends CommonChamiloDatabaseCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('translation:enable')
            ->setAliases(array('tel'))
            ->setDescription('Enables a (disabled) language')
            ->addArgument(
                'language',
                InputArgument::REQUIRED,
                'The English name for the language to enable.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $_configuration = $this->getHelper('configuration')->getConfiguration();
        $dbh = $this->getHelper('configuration')->getConnection();
        $lang = mysql_real_escape_string($input->getArgument('language'));
        $ls = "SELECT id, english_name, available FROM language WHERE english_name = '$lang'";
        $lq = mysql_query($ls);
        if ($lq === false) {
            $output->writeln('Error in query: '.mysql_error());
            return null;
        }
        $num = mysql_num_rows($lq);
        if ($num<1) {
            $output->writeln($lang.' language not found in the database. Please make sure you use an existing language name.');
            return null;
        }
        $lr = mysql_fetch_assoc($lq);
        if ($lr['available'] == 1) {
            $output->writeln($lang.' language is already enabled. Nothing to do.');
            return null;
        }
        // Everything is OK so far, enable the language
        $us = "UPDATE language SET available = 1 WHERE id = {$lr['id']}";
        $uq = mysql_query($us);
        if ($uq === false) {
            $output->writeln('Error in query: '.mysql_error());
        } else {
            $output->writeln($lang . ' language has been enabled.');
        }
        return null;
    }
}
