<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Used for the comparison of values.
 *
 * @author Daniel Holmes <daniel@danielholmes.org>
 */
abstract class AbstractComparison extends Constraint
{
    public $message;
    public $value;

    /**
     * {@inheritDoc}
     */
    public function __construct($options = null)
    {
        if (!isset($options['value'])) {
            throw new ConstraintDefinitionException(sprintf(
                'The %s constraint requires the "value" option to be set.',
                get_class($this)
            ));
        }

        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'value';
    }
}
