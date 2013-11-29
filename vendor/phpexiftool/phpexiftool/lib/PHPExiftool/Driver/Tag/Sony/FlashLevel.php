<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Sony;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class FlashLevel extends AbstractTag
{

    protected $Id = 45128;

    protected $Name = 'FlashLevel';

    protected $FullName = 'Sony::Main';

    protected $GroupName = 'Sony';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Sony';

    protected $g2 = 'Camera';

    protected $Type = 'int16s';

    protected $Writable = true;

    protected $Description = 'Flash Level';

    protected $flag_Permanent = true;

    protected $Values = array(
        '-32768' => array(
            'Id' => '-32768',
            'Label' => 'Low',
        ),
        '-3' => array(
            'Id' => '-3',
            'Label' => '-3/3',
        ),
        '-2' => array(
            'Id' => '-2',
            'Label' => '-2/3',
        ),
        '-1' => array(
            'Id' => '-1',
            'Label' => '-1/3',
        ),
        0 => array(
            'Id' => 0,
            'Label' => 'Normal',
        ),
        1 => array(
            'Id' => 1,
            'Label' => '+1/3',
        ),
        2 => array(
            'Id' => 2,
            'Label' => '+2/3',
        ),
        3 => array(
            'Id' => 3,
            'Label' => '+3/3',
        ),
        128 => array(
            'Id' => 128,
            'Label' => 'n/a',
        ),
        32767 => array(
            'Id' => 32767,
            'Label' => 'High',
        ),
    );

}
