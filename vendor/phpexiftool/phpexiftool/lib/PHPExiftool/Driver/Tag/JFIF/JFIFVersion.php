<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\JFIF;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class JFIFVersion extends AbstractTag
{

    protected $Id = 0;

    protected $Name = 'JFIFVersion';

    protected $FullName = 'JFIF::Main';

    protected $GroupName = 'JFIF';

    protected $g0 = 'JFIF';

    protected $g1 = 'JFIF';

    protected $g2 = 'Image';

    protected $Type = 'int8u';

    protected $Writable = false;

    protected $Description = 'JFIF Version';

    protected $MaxLength = 2;

}
