<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPCrs;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class PerspectiveScale extends AbstractTag
{

    protected $Id = 'PerspectiveScale';

    protected $Name = 'PerspectiveScale';

    protected $FullName = 'XMP::crs';

    protected $GroupName = 'XMP-crs';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-crs';

    protected $g2 = 'Image';

    protected $Type = 'integer';

    protected $Writable = true;

    protected $Description = 'Perspective Scale';

}
