<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\MetaIFD;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class SourceImageDirectory extends AbstractTag
{

    protected $Id = 50200;

    protected $Name = 'SourceImageDirectory';

    protected $FullName = 'Kodak::Meta';

    protected $GroupName = 'MetaIFD';

    protected $g0 = 'Meta';

    protected $g1 = 'MetaIFD';

    protected $g2 = 'Image';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'Source Image Directory';

}
