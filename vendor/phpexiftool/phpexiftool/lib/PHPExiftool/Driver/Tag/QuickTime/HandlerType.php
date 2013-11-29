<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\QuickTime;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class HandlerType extends AbstractTag
{

    protected $Id = 8;

    protected $Name = 'HandlerType';

    protected $FullName = 'QuickTime::Handler';

    protected $GroupName = 'QuickTime';

    protected $g0 = 'QuickTime';

    protected $g1 = 'QuickTime';

    protected $g2 = 'Video';

    protected $Type = 'undef';

    protected $Writable = false;

    protected $Description = 'Handler Type';

    protected $MaxLength = 4;

    protected $Values = array(
        'alis' => array(
            'Id' => 'alis',
            'Label' => 'Alias Data',
        ),
        'crsm' => array(
            'Id' => 'crsm',
            'Label' => 'Clock Reference',
        ),
        'hint' => array(
            'Id' => 'hint',
            'Label' => 'Hint Track',
        ),
        'ipsm' => array(
            'Id' => 'ipsm',
            'Label' => 'IPMP',
        ),
        'm7sm' => array(
            'Id' => 'm7sm',
            'Label' => 'MPEG-7 Stream',
        ),
        'mdir' => array(
            'Id' => 'mdir',
            'Label' => 'Metadata',
        ),
        'mdta' => array(
            'Id' => 'mdta',
            'Label' => 'Metadata Tags',
        ),
        'mjsm' => array(
            'Id' => 'mjsm',
            'Label' => 'MPEG-J',
        ),
        'ocsm' => array(
            'Id' => 'ocsm',
            'Label' => 'Object Content',
        ),
        'odsm' => array(
            'Id' => 'odsm',
            'Label' => 'Object Descriptor',
        ),
        'sdsm' => array(
            'Id' => 'sdsm',
            'Label' => 'Scene Description',
        ),
        'soun' => array(
            'Id' => 'soun',
            'Label' => 'Audio Track',
        ),
        'text' => array(
            'Id' => 'text',
            'Label' => 'Text',
        ),
        'tmcd' => array(
            'Id' => 'tmcd',
            'Label' => 'Time Code',
        ),
        'url ' => array(
            'Id' => 'url ',
            'Label' => 'URL',
        ),
        'vide' => array(
            'Id' => 'vide',
            'Label' => 'Video Track',
        ),
    );

}
