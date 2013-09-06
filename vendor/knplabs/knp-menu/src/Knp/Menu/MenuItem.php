<?php

namespace Knp\Menu;

use Knp\Menu\Util\MenuManipulator;

/**
 * Default implementation of the ItemInterface
 */
class MenuItem implements ItemInterface
{
    /**
     * Name of this menu item (used for id by parent menu)
     * @var string
     */
    protected $name = null;
    /**
     * Label to output, name is used by default
     * @var string
     */
    protected $label = null;
    /**
     * Attributes for the item link
     * @var array
     */
    protected $linkAttributes = array();
    /**
     * Attributes for the children list
     * @var array
     */
    protected $childrenAttributes = array();
    /**
     * Attributes for the item text
     * @var array
     */
    protected $labelAttributes = array();
    /**
     * Uri to use in the anchor tag
     * @var string
     */
    protected $uri = null;
    /**
     * Attributes for the item
     * @var array
     */
    protected $attributes = array();
    /**
     * Extra stuff associated to the item
     * @var array
     */
    protected $extras = array();

    /**
     * Whether the item is displayed
     * @var boolean
     */
    protected $display = true;
    /**
     * Whether the children of the item are displayed
     * @var boolean
     */
    protected $displayChildren = true;

    /**
     * Child items
     * @var ItemInterface[]
     */
    protected $children = array();
    /**
     * Parent item
     * @var ItemInterface|null
     */
    protected $parent = null;
    /**
     * whether the item is current. null means unknown
     * @var boolean|null
     */
    protected $isCurrent = null;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @deprecated this property is only part of the BC layer for deprecated methods
     * @var MenuManipulator
     */
    private $manipulator;

    /**
     * Class constructor
     *
     * @param string $name The name of this menu, which is how its parent will
     *                     reference it. Also used as label if label not specified
     * @param FactoryInterface $factory
     */
    public function __construct($name, FactoryInterface $factory)
    {
        $this->name = (string) $name;
        $this->factory = $factory;
    }

    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if ($this->name == $name) {
            return $this;
        }

        $parent = $this->getParent();
        if (null !== $parent && isset($parent[$name])) {
            throw new \InvalidArgumentException('Cannot rename item, name is already used by sibling.');
        }

        $oldName = $this->name;
        $this->name = $name;

        if (null !== $parent) {
            $names = array_keys($parent->getChildren());
            $items = array_values($parent->getChildren());

            $offset = array_search($oldName, $names);
            $names[$offset] = $name;

            $parent->setChildren(array_combine($names, $items));
        }

        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getLabel()
    {
        return ($this->label !== null) ? $this->label : $this->name;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    public function setLinkAttributes(array $linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    public function getLinkAttribute($name, $default = null)
    {
        if (isset($this->linkAttributes[$name])) {
            return $this->linkAttributes[$name];
        }

        return $default;
    }

    public function setLinkAttribute($name, $value)
    {
        $this->linkAttributes[$name] = $value;

        return $this;
    }

    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    public function setChildrenAttributes(array $childrenAttributes)
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    public function getChildrenAttribute($name, $default = null)
    {
        if (isset($this->childrenAttributes[$name])) {
            return $this->childrenAttributes[$name];
        }

        return $default;
    }

    public function setChildrenAttribute($name, $value)
    {
        $this->childrenAttributes[$name] = $value;

        return $this;
    }

    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    public function getLabelAttribute($name, $default = null)
    {
        if (isset($this->labelAttributes[$name])) {
            return $this->labelAttributes[$name];
        }

        return $default;
    }

    public function setLabelAttribute($name, $value)
    {
        $this->labelAttributes[$name] = $value;

        return $this;
    }

    public function getExtras()
    {
        return $this->extras;
    }

    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    public function getExtra($name, $default = null)
    {
        if (isset($this->extras[$name])) {
            return $this->extras[$name];
        }

        return $default;
    }

    public function setExtra($name, $value)
    {
        $this->extras[$name] = $value;

        return $this;
    }

    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    public function setDisplayChildren($bool)
    {
        $this->displayChildren = (bool) $bool;

        return $this;
    }

    public function isDisplayed()
    {
        return $this->display;
    }

    public function setDisplay($bool)
    {
        $this->display = (bool) $bool;

        return $this;
    }

    public function addChild($child, array $options = array())
    {
        if (!$child instanceof ItemInterface) {
            $child = $this->factory->createItem($child, $options);
        } elseif (null !== $child->getParent()) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }

        $child->setParent($this);

        $this->children[$child->getName()] = $child;

        return $child;
    }

    public function getChild($name)
    {
        return isset($this->children[$name]) ? $this->children[$name] : null;
    }

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param integer $position Position to move child to.
     *
     * @return ItemInterface
     */
    public function moveToPosition($position)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        $this->getManipulator()->moveToPosition($this, $position);

        return $this;
    }

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param ItemInterface $child    Child to move.
     * @param integer       $position Position to move child to.
     *
     * @return ItemInterface
     */
    public function moveChildToPosition(ItemInterface $child, $position)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        $this->getManipulator()->moveChildToPosition($this, $child, $position);

        return $this;
    }

    /**
     * Moves child to first position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @return ItemInterface
     */
    public function moveToFirstPosition()
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        $this->getManipulator()->moveToFirstPosition($this);

        return $this;
    }

    /**
     * Moves child to last position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @return ItemInterface
     */
    public function moveToLastPosition()
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        $this->getManipulator()->moveToLastPosition($this);

        return $this;
    }

    public function reorderChildren($order)
    {
        if (count($order) != $this->count()) {
            throw new \InvalidArgumentException('Cannot reorder children, order does not contain all children.');
        }

        $newChildren = array();

        foreach ($order as $name) {
            if (!isset($this->children[$name])) {
                throw new \InvalidArgumentException('Cannot find children named ' . $name);
            }

            $child = $this->children[$name];
            $newChildren[$name] = $child;
        }

        $this->children = $newChildren;

        return $this;
    }

    public function copy()
    {
        $newMenu = clone $this;
        $newMenu->children = array();
        $newMenu->setParent(null);
        foreach ($this->getChildren() as $child) {
            $newMenu->addChild($child->copy());
        }

        return $newMenu;
    }

    /**
     * Get slice of menu as another menu.
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param mixed $offset Name of child, child object, or numeric offset.
     * @param mixed $length Name of child, child object, or numeric length.
     *
     * @return ItemInterface
     */
    public function slice($offset, $length = null)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        return $this->getManipulator()->slice($this, $offset, $length);
    }

    /**
     * Split menu into two distinct menus.
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param mixed $length Name of child, child object, or numeric length.
     *
     * @return array Array with two menus, with "primary" and "secondary" key
     */
    public function split($length)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        return $this->getManipulator()->split($this, $length);
    }

    public function getLevel()
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
    }

    public function getRoot()
    {
        $obj = $this;
        do {
            $found = $obj;
        } while ($obj = $obj->getParent());

        return $found;
    }

    public function isRoot()
    {
        return null === $this->parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(ItemInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

    public function removeChild($name)
    {
        $name = $name instanceof ItemInterface ? $name->getName() : $name;

        if (isset($this->children[$name])) {
            // unset the child and reset it so it looks independent
            $this->children[$name]->setParent(null);
            unset($this->children[$name]);
        }

        return $this;
    }

    public function getFirstChild()
    {
        return reset($this->children);
    }

    public function getLastChild()
    {
        return end($this->children);
    }

    public function hasChildren()
    {
        foreach ($this->children as $child) {
            if ($child->isDisplayed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * A string representation of this menu item
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param string $separator
     *
     * @return string
     */
    public function getPathAsString($separator = ' > ')
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        return $this->getManipulator()->getPathAsString($this, $separator);
    }

    /**
     * Renders an array ready to be used for breadcrumbs.
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param mixed $subItem A string or array to append onto the end of the array
     *
     * @return array
     */
    public function getBreadcrumbsArray($subItem = null)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        return $this->getManipulator()->getBreadcrumbsArray($this, $subItem);
    }

    public function setCurrent($bool)
    {
        $this->isCurrent = $bool;

        return $this;
    }

    public function isCurrent()
    {
        return $this->isCurrent;
    }

    public function isLast()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getLastChild() === $this;
    }

    public function isFirst()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getFirstChild() === $this;
    }

    public function actsLikeFirst()
    {
        // root items are never "marked" as first
        if ($this->isRoot()) {
            return false;
        }

        // A menu acts like first only if it is displayed
        if (!$this->isDisplayed()) {
            return false;
        }

        // if we're first and visible, we're first, period.
        if ($this->isFirst()) {
            return true;
        }

        $children = $this->getParent()->getChildren();
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->isDisplayed()) {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    public function actsLikeLast()
    {
        // root items are never "marked" as last
        if ($this->isRoot()) {
            return false;
        }

        // A menu acts like last only if it is displayed
        if (!$this->isDisplayed()) {
            return false;
        }

        // if we're last and visible, we're last, period.
        if ($this->isLast()) {
            return true;
        }

        $children = array_reverse($this->getParent()->getChildren());
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->isDisplayed()) {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    /**
     * Calls a method recursively on all of the children of this item
     *
     * Provides a fluent interface
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return ItemInterface
     */
    public function callRecursively($method, $arguments = array())
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        $this->getManipulator()->callRecursively($this, $method, $arguments);

        return $this;
    }

    /**
     * Exports this menu item to an array
     *
     * @deprecated Use \Knp\Menu\Util\MenuManipulator
     *
     * @param integer $depth
     *
     * @return array
     */
    public function toArray($depth = null)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Util\MenuManipulator instead', E_USER_DEPRECATED);

        return $this->getManipulator()->toArray($this, $depth);
    }

    /**
     * Implements Countable
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Implements IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet($name)
    {
        return $this->getChild($name);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet($name, $value)
    {
        return $this->addChild($name)->setLabel($value);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset($name)
    {
        $this->removeChild($name);
    }

    /**
     * @return MenuManipulator
     */
    private function getManipulator()
    {
        if (null === $this->manipulator) {
            $this->manipulator = new MenuManipulator();
        }

        return $this->manipulator;
    }
}
