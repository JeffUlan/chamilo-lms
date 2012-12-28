<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * PhpFileDumper generates php files from a message catalogue.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
class PhpFileDumper extends FileDumper
{
    /**
     * {@inheritDoc}
     */
    protected function format(MessageCatalogue $messages, $domain)
    {
        $output = "<?php\n\nreturn ".var_export($messages->all($domain), true).";\n";

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtension()
    {
        return 'php';
    }
}
