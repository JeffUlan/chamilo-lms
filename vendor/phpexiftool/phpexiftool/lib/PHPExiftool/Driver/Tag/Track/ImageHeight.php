<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Track;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ImageHeight extends AbstractTag
{

    protected $Id = 20;

    protected $Name = 'ImageHeight';

    protected $FullName = 'QuickTime::TrackHeader';

    protected $GroupName = 'Track#';

    protected $g0 = 'QuickTime';

    protected $g1 = 'Track#';

    protected $g2 = 'Video';

    protected $Type = 'int32u';

    protected $Writable = false;

    protected $Description = 'Image Height';

}
