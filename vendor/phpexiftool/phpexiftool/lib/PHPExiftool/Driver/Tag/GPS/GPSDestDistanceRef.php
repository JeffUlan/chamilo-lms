<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\GPS;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class GPSDestDistanceRef extends AbstractTag
{

    protected $Id = 25;

    protected $Name = 'GPSDestDistanceRef';

    protected $FullName = 'GPS::Main';

    protected $GroupName = 'GPS';

    protected $g0 = 'EXIF';

    protected $g1 = 'GPS';

    protected $g2 = 'Location';

    protected $Type = 'string';

    protected $Writable = true;

    protected $Description = 'GPS Dest Distance Ref';

    protected $Values = array(
        'K' => array(
            'Id' => 'K',
            'Label' => 'Kilometers',
        ),
        'M' => array(
            'Id' => 'M',
            'Label' => 'Miles',
        ),
        'N' => array(
            'Id' => 'N',
            'Label' => 'Nautical Miles',
        ),
    );

}
