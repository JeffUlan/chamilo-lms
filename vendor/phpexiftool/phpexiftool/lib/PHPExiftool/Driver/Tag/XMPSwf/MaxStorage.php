<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPSwf;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class MaxStorage extends AbstractTag
{

    protected $Id = 'maxstorage';

    protected $Name = 'MaxStorage';

    protected $FullName = 'XMP::swf';

    protected $GroupName = 'XMP-swf';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-swf';

    protected $g2 = 'Image';

    protected $Type = 'integer';

    protected $Writable = true;

    protected $Description = 'Max Storage';

}
