<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPAppleFi;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class AngleInfoRoll extends AbstractTag
{

    protected $Id = 'AngleInfoRoll';

    protected $Name = 'AngleInfoRoll';

    protected $FullName = 'XMP::apple_fi';

    protected $GroupName = 'XMP-apple-fi';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-apple-fi';

    protected $g2 = 'Image';

    protected $Type = 'integer';

    protected $Writable = true;

    protected $Description = 'Angle Info Roll';

}
