<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\PanasonicRaw;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class DistortionParam09 extends AbstractTag
{

    protected $Id = 9;

    protected $Name = 'DistortionParam09';

    protected $FullName = 'PanasonicRaw::DistortionInfo';

    protected $GroupName = 'PanasonicRaw';

    protected $g0 = 'PanasonicRaw';

    protected $g1 = 'PanasonicRaw';

    protected $g2 = 'Other';

    protected $Type = 'int16s';

    protected $Writable = true;

    protected $Description = 'Distortion Param 09';

}
