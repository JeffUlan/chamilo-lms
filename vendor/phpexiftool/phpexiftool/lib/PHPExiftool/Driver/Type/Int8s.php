<?php

/*
 * This file is part of PHPExifTool.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Type;

use PHPExiftool\Driver\AbstractType;

class Int8s extends AbstractType
{

    protected $ExiftoolName = 'int8s';

    protected $PHPMap = 'int';

}
