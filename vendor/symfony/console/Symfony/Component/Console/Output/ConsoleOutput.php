<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * ConsoleOutput is the default class for all CLI output. It uses STDOUT.
 *
 * This class is a convenient wrapper around `StreamOutput`.
 *
 *     $output = new ConsoleOutput();
 *
 * This is equivalent to:
 *
 *     $output = new StreamOutput(fopen('php://stdout', 'w'));
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class ConsoleOutput extends StreamOutput implements ConsoleOutputInterface
{
    private $stderr;

    /**
     * Constructor.
     *
     * @param integer                  $verbosity The verbosity level (self::VERBOSITY_QUIET, self::VERBOSITY_NORMAL,
     *                                                                 self::VERBOSITY_VERBOSE)
     * @param Boolean                  $decorated Whether to decorate messages or not (null for auto-guessing)
     * @param OutputFormatterInterface $formatter Output formatter instance
     *
     * @api
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        $outputStream = 'php://stdout';
        if (!$this->hasStdoutSupport()) {
            $outputStream = 'php://output';
        }

        parent::__construct(fopen($outputStream, 'w'), $verbosity, $decorated, $formatter);

        $this->stderr = new StreamOutput(fopen('php://stderr', 'w'), $verbosity, $decorated, $formatter);
    }

    public function setDecorated($decorated)
    {
        parent::setDecorated($decorated);
        $this->stderr->setDecorated($decorated);
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        parent::setFormatter($formatter);
        $this->stderr->setFormatter($formatter);
    }

    public function setVerbosity($level)
    {
        parent::setVerbosity($level);
        $this->stderr->setVerbosity($level);
    }

    /**
     * @return OutputInterface
     */
    public function getErrorOutput()
    {
        return $this->stderr;
    }

    public function setErrorOutput(OutputInterface $error)
    {
        $this->stderr = $error;
    }

    /**
     * Returns true if current environment supports writing console output to
     * STDOUT.
     *
     * IBM iSeries (OS400) exhibits character-encoding issues when writing to
     * STDOUT and doesn't properly convert ASCII to EBCDIC, resulting in garbage
     * output.
     *
     * @return boolean
     */
    protected function hasStdoutSupport()
    {
        return ('OS400' != php_uname('s'));
    }
}
