<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Console\Helper;

use Symfony\Bundle\FrameworkBundle\Console\Descriptor\JsonDescriptor;
use Symfony\Bundle\FrameworkBundle\Console\Descriptor\MarkdownDescriptor;
use Symfony\Bundle\FrameworkBundle\Console\Descriptor\TextDescriptor;
use Symfony\Bundle\FrameworkBundle\Console\Descriptor\XmlDescriptor;
use Symfony\Component\Console\Helper\DescriptorHelper as BaseDescriptorHelper;

/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class DescriptorHelper extends BaseDescriptorHelper
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this
            ->register('txt',  new TextDescriptor())
            ->register('xml',  new XmlDescriptor())
            ->register('json', new JsonDescriptor())
            ->register('md',   new MarkdownDescriptor())
        ;
    }
}
