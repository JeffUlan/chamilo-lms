<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\CanonCustom;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class AEBShotCount extends AbstractTag
{

    protected $Id = 262;

    protected $Name = 'AEBShotCount';

    protected $FullName = 'CanonCustom::Functions2';

    protected $GroupName = 'CanonCustom';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'CanonCustom';

    protected $g2 = 'Camera';

    protected $Type = 'int32s';

    protected $Writable = true;

    protected $Description = 'AEB Shot Count';

    protected $flag_Permanent = true;

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 3,
        ),
        1 => array(
            'Id' => 1,
            'Label' => 2,
        ),
        2 => array(
            'Id' => 2,
            'Label' => 5,
        ),
        3 => array(
            'Id' => 3,
            'Label' => 7,
        ),
    );

}
