<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Locale\Stub\DateFormat;

/**
 * Parser and formatter for date formats
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
abstract class Transformer
{
    /**
     * Format a value using a configured DateTime as date/time source
     *
     *
     * @param \DateTime $dateTime A DateTime object to be used to generate the formatted value
     * @param int       $length   The formatted value string length
     *
     * @return string               The formatted value
     */
    abstract public function format(\DateTime $dateTime, $length);

    /**
     * Returns a reverse matching regular expression of a string generated by format()
     *
     * @param int $length The length of the value to be reverse matched
     *
     * @return string           The reverse matching regular expression
     */
    abstract public function getReverseMatchingRegExp($length);

    /**
     * Extract date options from a matched value returned by the processing of the reverse matching
     * regular expression
     *
     * @param string $matched The matched value
     * @param int    $length  The length of the Transformer pattern string
     *
     * @return array             An associative array
     */
    abstract public function extractDateOptions($matched, $length);

    /**
     * Pad a string with zeros to the left
     *
     * @param string $value  The string to be padded
     * @param int    $length The length to pad
     *
     * @return string           The padded string
     */
    protected function padLeft($value, $length)
    {
        return str_pad($value, $length, '0', STR_PAD_LEFT);
    }
}
