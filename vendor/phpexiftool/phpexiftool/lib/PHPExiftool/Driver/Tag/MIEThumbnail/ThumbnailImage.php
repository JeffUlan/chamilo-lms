<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\MIEThumbnail;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ThumbnailImage extends AbstractTag
{

    protected $Id = 'data';

    protected $Name = 'ThumbnailImage';

    protected $FullName = 'MIE::Thumbnail';

    protected $GroupName = 'MIE-Thumbnail';

    protected $g0 = 'MIE';

    protected $g1 = 'MIE-Thumbnail';

    protected $g2 = 'Image';

    protected $Type = 'undef';

    protected $Writable = true;

    protected $Description = 'Thumbnail Image';

    protected $flag_Binary = true;

}
