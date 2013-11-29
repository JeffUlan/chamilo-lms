<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\ICCView;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ViewingCondSurround extends AbstractTag
{

    protected $Id = 20;

    protected $Name = 'ViewingCondSurround';

    protected $FullName = 'ICC_Profile::ViewingConditions';

    protected $GroupName = 'ICC-view';

    protected $g0 = 'ICC_Profile';

    protected $g1 = 'ICC-view';

    protected $g2 = 'Image';

    protected $Type = 'fixed32s';

    protected $Writable = false;

    protected $Description = 'Viewing Cond Surround';

    protected $MaxLength = 3;

}
