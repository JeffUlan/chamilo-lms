<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPMwgKw;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class HierarchicalKeywords1 extends AbstractTag
{

    protected $Id = 'KeywordsHierarchyKeyword';

    protected $Name = 'HierarchicalKeywords1';

    protected $FullName = 'XMP::mwg_kw';

    protected $GroupName = 'XMP-mwg-kw';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-mwg-kw';

    protected $g2 = 'Image';

    protected $Type = 'string';

    protected $Writable = true;

    protected $Description = 'Hierarchical Keywords 1';

    protected $flag_List = true;

}
