<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\GeoTiff;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ProjStdParallel2 extends AbstractTag
{

    protected $Id = 3079;

    protected $Name = 'ProjStdParallel2';

    protected $FullName = 'GeoTiff::Main';

    protected $GroupName = 'GeoTiff';

    protected $g0 = 'GeoTiff';

    protected $g1 = 'GeoTiff';

    protected $g2 = 'Location';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'Proj Std Parallel 2';

}
