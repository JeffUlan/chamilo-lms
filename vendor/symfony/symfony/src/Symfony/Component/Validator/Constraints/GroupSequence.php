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

use Symfony\Component\Validator\Exception\OutOfBoundsException;

/**
 * A sequence of validation groups.
 *
 * When validating a group sequence, each group will only be validated if all
 * of the previous groups in the sequence succeeded. For example:
 *
 *     $validator->validate($address, null, new GroupSequence('Basic', 'Strict'));
 *
 * In the first step, all constraints that belong to the group "Basic" will be
 * validated. If none of the constraints fail, the validator will then validate
 * the constraints in group "Strict". This is useful, for example, if "Strict"
 * contains expensive checks that require a lot of CPU or slow, external
 * services. You usually don't want to run expensive checks if any of the cheap
 * checks fail.
 *
 * When adding metadata to a class, you can override the "Default" group of
 * that class with a group sequence:
 *
 *     /**
 *      * @GroupSequence({"Address", "Strict"})
 *      *\/
 *     class Address
 *     {
 *         // ...
 *     }
 *
 * Whenever you validate that object in the "Default" group, the group sequence
 * will be validated:
 *
 *     $validator->validate($address);
 *
 * If you want to execute the constraints of the "Default" group for a class
 * with an overridden default group, pass the class name as group name instead:
 *
 *     $validator->validate($address, null, "Address")
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @api
 */
class GroupSequence implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * The groups in the sequence
     *
     * @var string[]|GroupSequence[]
     */
    public $groups;

    /**
     * The group in which cascaded objects are validated when validating
     * this sequence.
     *
     * By default, cascaded objects are validated in each of the groups of
     * the sequence.
     *
     * If a class has a group sequence attached, that sequence replaces the
     * "Default" group. When validating that class in the "Default" group, the
     * group sequence is used instead, but still the "Default" group should be
     * cascaded to other objects.
     *
     * @var string|GroupSequence
     */
    public $cascadedGroup;

    /**
     * Creates a new group sequence.
     *
     * @param string[] $groups The groups in the sequence
     */
    public function __construct(array $groups)
    {
        // Support for Doctrine annotations
        $this->groups = isset($groups['value']) ? $groups['value'] : $groups;
    }

    /**
     * Returns an iterator for this group.
     *
     * @return \Traversable The iterator
     *
     * @see \IteratorAggregate::getIterator()
     *
     * @deprecated Implemented for backwards compatibility with Symfony < 2.5.
     *             To be removed in Symfony 3.0.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->groups);
    }

    /**
     * Returns whether the given offset exists in the sequence.
     *
     * @param int     $offset The offset
     *
     * @return bool    Whether the offset exists
     *
     * @deprecated Implemented for backwards compatibility with Symfony < 2.5.
     *             To be removed in Symfony 3.0.
     */
    public function offsetExists($offset)
    {
        return isset($this->groups[$offset]);
    }

    /**
     * Returns the group at the given offset.
     *
     * @param int     $offset The offset
     *
     * @return string The group a the given offset
     *
     * @throws OutOfBoundsException If the object does not exist
     *
     * @deprecated Implemented for backwards compatibility with Symfony < 2.5.
     *             To be removed in Symfony 3.0.
     */
    public function offsetGet($offset)
    {
        if (!isset($this->groups[$offset])) {
            throw new OutOfBoundsException(sprintf(
                'The offset "%s" does not exist.',
                $offset
            ));
        }

        return $this->groups[$offset];
    }

    /**
     * Sets the group at the given offset.
     *
     * @param int     $offset The offset
     * @param string  $value  The group name
     *
     * @deprecated Implemented for backwards compatibility with Symfony < 2.5.
     *             To be removed in Symfony 3.0.
     */
    public function offsetSet($offset, $value)
    {
        if (null !== $offset) {
            $this->groups[$offset] = $value;

            return;
        }

        $this->groups[] = $value;
    }

    /**
     * Removes the group at the given offset.
     *
     * @param int     $offset The offset
     *
     * @deprecated Implemented for backwards compatibility with Symfony < 2.5.
     *             To be removed in Symfony 3.0.
     */
    public function offsetUnset($offset)
    {
        unset($this->groups[$offset]);
    }

    /**
     * Returns the number of groups in the sequence.
     *
     * @return int     The number of groups
     *
     * @deprecated Implemented for backwards compatibility with Symfony < 2.5.
     *             To be removed in Symfony 3.0.
     */
    public function count()
    {
        return count($this->groups);
    }
}
