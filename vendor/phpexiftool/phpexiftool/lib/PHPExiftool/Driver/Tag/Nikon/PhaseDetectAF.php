<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Nikon;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class PhaseDetectAF extends AbstractTag
{

    protected $Id = 6;

    protected $Name = 'PhaseDetectAF';

    protected $FullName = 'Nikon::AFInfo2';

    protected $GroupName = 'Nikon';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Nikon';

    protected $g2 = 'Camera';

    protected $Type = 'int8u';

    protected $Writable = true;

    protected $Description = 'Phase Detect AF';

    protected $flag_Permanent = true;

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 'Off',
        ),
        1 => array(
            'Id' => 1,
            'Label' => 'On (51-point)',
        ),
        2 => array(
            'Id' => 2,
            'Label' => 'On (11-point)',
        ),
        3 => array(
            'Id' => 3,
            'Label' => 'On (39-point)',
        ),
    );

}
