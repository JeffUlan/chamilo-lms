<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\NikonCustom;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class LiveViewAFAreaMode extends AbstractTag
{

    protected $Id = '34.1';

    protected $Name = 'LiveViewAFAreaMode';

    protected $FullName = 'NikonCustom::SettingsD7000';

    protected $GroupName = 'NikonCustom';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'NikonCustom';

    protected $g2 = 'Camera';

    protected $Type = 'int8u';

    protected $Writable = true;

    protected $Description = 'Live View AF Area Mode';

    protected $flag_Permanent = true;

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 'Face-Priority',
        ),
        32 => array(
            'Id' => 32,
            'Label' => 'NormalArea',
        ),
        64 => array(
            'Id' => 64,
            'Label' => 'WideArea',
        ),
        96 => array(
            'Id' => 96,
            'Label' => 'SubjectTracking',
        ),
    );

}
