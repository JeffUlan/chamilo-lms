<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\ExifIFD;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class MakerNotePentax4 extends AbstractTag
{

    protected $Id = 'mixed';

    protected $Name = 'MakerNotePentax4';

    protected $FullName = 'mixed';

    protected $GroupName = 'ExifIFD';

    protected $g0 = 'mixed';

    protected $g1 = 'mixed';

    protected $g2 = 'Image';

    protected $Type = 'undef';

    protected $Writable = true;

    protected $Description = 'Maker Note Pentax 4';

    protected $local_g1 = 'ExifIFD';

    protected $flag_Binary = true;

    protected $flag_Permanent = true;

    protected $Index = 48;

}
