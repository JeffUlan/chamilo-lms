<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Panasonic;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ImageQuality extends AbstractTag
{

    protected $Id = 1;

    protected $Name = 'ImageQuality';

    protected $FullName = 'Panasonic::Main';

    protected $GroupName = 'Panasonic';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Panasonic';

    protected $g2 = 'Camera';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'Image Quality';

    protected $flag_Permanent = true;

    protected $Values = array(
        2 => array(
            'Id' => 2,
            'Label' => 'High',
        ),
        3 => array(
            'Id' => 3,
            'Label' => 'Normal',
        ),
        6 => array(
            'Id' => 6,
            'Label' => 'Very High',
        ),
        7 => array(
            'Id' => 7,
            'Label' => 'Raw',
        ),
        9 => array(
            'Id' => 9,
            'Label' => 'Motion Picture',
        ),
    );

}
