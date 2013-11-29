<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\H264;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class Make extends AbstractTag
{

    protected $Id = 0;

    protected $Name = 'Make';

    protected $FullName = 'H264::MakeModel';

    protected $GroupName = 'H264';

    protected $g0 = 'H264';

    protected $g1 = 'H264';

    protected $g2 = 'Camera';

    protected $Type = 'int16u';

    protected $Writable = false;

    protected $Description = 'Make';

    protected $Values = array(
        259 => array(
            'Id' => 259,
            'Label' => 'Panasonic',
        ),
        264 => array(
            'Id' => 264,
            'Label' => 'Sony',
        ),
        4113 => array(
            'Id' => 4113,
            'Label' => 'Canon',
        ),
    );

}
