<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\Console\Descriptor;

use Symfony\Bundle\FrameworkBundle\Console\Descriptor\MarkdownDescriptor;

class MarkdownDescriptorTest extends AbstractDescriptorTest
{
    protected function getDescriptor()
    {
        return new MarkdownDescriptor();
    }

    protected function getFormat()
    {
        return 'md';
    }
}
