<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\IPTC;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class LookupTable extends AbstractTag
{

    protected $Id = 80;

    protected $Name = 'LookupTable';

    protected $FullName = 'IPTC::NewsPhoto';

    protected $GroupName = 'IPTC';

    protected $g0 = 'IPTC';

    protected $g1 = 'IPTC';

    protected $g2 = 'Image';

    protected $Type = '?';

    protected $Writable = true;

    protected $Description = 'Lookup Table';

    protected $flag_Binary = true;

}
