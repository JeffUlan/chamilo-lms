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
class FacesDetected extends AbstractTag
{

    protected $Id = 'mixed';

    protected $Name = 'FacesDetected';

    protected $FullName = 'mixed';

    protected $GroupName = 'Sony';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Sony';

    protected $g2 = 'mixed';

    protected $Type = 'mixed';

    protected $Writable = true;

    protected $Description = 'Faces Detected';

    protected $flag_Permanent = true;

    protected $Values = array(
        '-1' => array(
            'Id' => '-1',
            'Label' => 'n/a',
        ),
    );

}
