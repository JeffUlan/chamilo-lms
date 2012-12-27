<?php

namespace Knp\Menu;

/**
 * Interface implemented by a menu item.
 *
 * It roughly represents a single <li> tag and is what you should interact with
 * most of the time by default.
 * Originally taken from ioMenuPlugin (http://github.com/weaverryan/ioMenuPlugin)
 */
interface ItemInterface extends  \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @param FactoryInterface $factory
     *
     * @return ItemInterface
     */
    public function setFactory(FactoryInterface $factory);

    /**
     * @return string
     */
    public function getName();

    /**
     * Renames the item.
     *
     * This method must also update the key in the parent.
     *
     * Provides a fluent interface
     *
     * @param string $name
     *
     * @return ItemInterface
     *
     * @throws \InvalidArgumentException if the name is already used by a sibling
     */
    public function setName($name);

    /**
     * Get the uri for a menu item
     *
     * @return string
     */
    public function getUri();

    /**
     * Set the uri for a menu item
     *
     * Provides a fluent interface
     *
     * @param string $uri The uri to set on this menu item
     *
     * @return ItemInterface
     */
    public function setUri($uri);

    /**
     * Returns the label that will be used to render this menu item
     *
     * Defaults to the name of no label was specified
     *
     * @return string
     */
    public function getLabel();

    /**
     * Provides a fluent interface
     *
     * @param string $label The text to use when rendering this menu item
     *
     * @return ItemInterface
     */
    public function setLabel($label);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * Provides a fluent interface
     *
     * @param array $attributes
     *
     * @return ItemInterface
     */
    public function setAttributes(array $attributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return ItemInterface
     */
    public function setAttribute($name, $value);

    /**
     * @return array
     */
    public function getLinkAttributes();

    /**
     * Provides a fluent interface
     *
     * @param array $linkAttributes
     *
     * @return ItemInterface
     */
    public function setLinkAttributes(array $linkAttributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     *
     * @return mixed
     */
    public function getLinkAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param string $value
     *
     * @return ItemInterface
     */
    public function setLinkAttribute($name, $value);

    /**
     * @return array
     */
    public function getChildrenAttributes();

    /**
     * Provides a fluent interface
     *
     * @param array $childrenAttributes
     *
     * @return ItemInterface
     */
    public function setChildrenAttributes(array $childrenAttributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     *
     * @return mixed
     */
    public function getChildrenAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param string $value
     *
     * @return ItemInterface
     */
    public function setChildrenAttribute($name, $value);

    /**
     * @return array
     */
    public function getLabelAttributes();

    /**
     * Provides a fluent interface
     *
     * @param array $labelAttributes
     *
     * @return ItemInterface
     */
    public function setLabelAttributes(array $labelAttributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     *
     * @return mixed
     */
    public function getLabelAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return ItemInterface
     */
    public function setLabelAttribute($name, $value);

    /**
     * @return array
     */
    public function getExtras();

    /**
     * Provides a fluent interface
     *
     * @param array $extras
     *
     * @return ItemInterface
     */
    public function setExtras(array $extras);

    /**
     * @param string $name    The name of the extra to return
     * @param mixed  $default The value to return if the extra doesn't exist
     *
     * @return mixed
     */
    public function getExtra($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return ItemInterface
     */
    public function setExtra($name, $value);

    /**
     * Whether or not this menu item should show its children.
     *
     * @return boolean
     */
    public function getDisplayChildren();

    /**
     * Set whether or not this menu item should show its children
     *
     * Provides a fluent interface
     *
     * @param boolean $bool
     *
     * @return ItemInterface
     */
    public function setDisplayChildren($bool);

    /**
     * Whether or not to display this menu item
     *
     * @return boolean
     */
    public function isDisplayed();

    /**
     * Set whether or not this menu should be displayed
     *
     * Provides a fluent interface
     *
     * @param boolean $bool
     *
     * @return ItemInterface
     */
    public function setDisplay($bool);

    /**
     * Add a child menu item to this menu
     *
     * Returns the child item
     *
     * @param ItemInterface|string $child   An ItemInterface instance or the name of a new item to create
     * @param array                $options If creating a new item, the options passed to the factory for the item
     *
     * @return ItemInterface
     * @throws \InvalidArgumentException if the item is already in a tree
     */
    public function addChild($child, array $options = array());

    /**
     * Returns the child menu identified by the given name
     *
     * @param string $name Then name of the child menu to return
     *
     * @return ItemInterface|null
     */
    public function getChild($name);

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @param integer $position Position to move child to.
     *
     * @return ItemInterface
     */
    public function moveToPosition($position);

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @param ItemInterface $child    Child to move.
     * @param integer       $position Position to move child to.
     *
     * @return ItemInterface
     */
    public function moveChildToPosition(ItemInterface $child, $position);

    /**
     * Moves child to first position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @return ItemInterface
     */
    public function moveToFirstPosition();

    /**
     * Moves child to last position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @return ItemInterface
     */
    public function moveToLastPosition();

    /**
     * Reorder children.
     *
     * Provides a fluent interface
     *
     * @param array $order New order of children.
     *
     * @return ItemInterface
     */
    public function reorderChildren($order);

    /**
     * Makes a deep copy of menu tree. Every item is copied as another object.
     *
     * @return ItemInterface
     */
    public function copy();

    /**
     * Get slice of menu as another menu.
     *
     * If offset and/or length are numeric, it works like in array_slice function:
     *
     *   If offset is non-negative, slice will start at the offset.
     *   If offset is negative, slice will start that far from the end.
     *
     *   If length is null, slice will have all elements.
     *   If length is positive, slice will have that many elements.
     *   If length is negative, slice will stop that far from the end.
     *
     * It's possible to mix names/object/numeric, for example:
     *   slice("child1", 2);
     *   slice(3, $child5);
     * Note: when using a child as limit, it will not be included in the returned menu.
     * the slice is done before this menu.
     *
     * @param mixed $offset Name of child, child object, or numeric offset.
     * @param mixed $length Name of child, child object, or numeric length.
     *
     * @return ItemInterface
     */
    public function slice($offset, $length = 0);

    /**
     * Split menu into two distinct menus.
     *
     * @param mixed $length Name of child, child object, or numeric length.
     *
     * @return array Array with two menus, with "primary" and "secondary" key
     */
    public function split($length);

    /**
     * Returns the level of this menu item
     *
     * The root menu item is 0, followed by 1, 2, etc
     *
     * @return integer
     */
    public function getLevel();

    /**
     * Returns the root ItemInterface of this menu tree
     *
     * @return ItemInterface
     */
    public function getRoot();

    /**
     * Returns whether or not this menu item is the root menu item
     *
     * @return boolean
     */
    public function isRoot();

    /**
     * @return ItemInterface|null
     */
    public function getParent();

    /**
     * Used internally when adding and removing children
     *
     * Provides a fluent interface
     *
     * @param ItemInterface|null $parent
     *
     * @return ItemInterface
     */
    public function setParent(ItemInterface $parent = null);

    /**
     * Return the children as an array of ItemInterface objects
     *
     * @return array
     */
    public function getChildren();

    /**
     * Provides a fluent interface
     *
     * @param array $children An array of ItemInterface objects
     *
     * @return ItemInterface
     */
    public function setChildren(array $children);

    /**
     * Removes a child from this menu item
     *
     * Provides a fluent interface
     *
     * @param ItemInterface|string $name The name of ItemInterface instance or the ItemInterface to remove
     *
     * @return ItemInterface
     */
    public function removeChild($name);

    /**
     * @return ItemInterface
     */
    public function getFirstChild();

    /**
     * @return ItemInterface
     */
    public function getLastChild();

    /**
     * Returns whether or not this menu items has viewable children
     *
     * This menu MAY have children, but this will return false if the current
     * user does not have access to view any of those items
     *
     * @return boolean
     */
    public function hasChildren();

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     *
     * @param string $separator
     *
     * @return string
     */
    public function getPathAsString($separator = ' > ');

    /**
     * Renders an array ready to be used for breadcrumbs.
     *
     * Each element in the array will be an array with 3 keys:
     * - `label` containing the label of the item
     * - `url` containing the url of the item (may be `null`)
     * - `item` containing the original item (may be `null` for the extra items)
     *
     * The subItem can be one of the following forms
     *   * 'subItem'
     *   * ItemInterface object
     *   * array('subItem' => '@homepage')
     *   * array('subItem1', 'subItem2')
     *   * array(array('label' => 'subItem1', 'url' => '@homepage'), array('label' => 'subItem2'))
     *
     * @param mixed $subItem A string or array to append onto the end of the array
     *
     * @return array
     */
    public function getBreadcrumbsArray($subItem = null);

    /**
     * Sets whether or not this menu item is "current".
     *
     * If the state is unknown, use null.
     *
     * Provides a fluent interface
     *
     * @param boolean|null $bool Specify that this menu item is current
     *
     * @return ItemInterface
     */
    public function setCurrent($bool);

    /**
     * Gets whether or not this menu item is "current".
     *
     * @return boolean|null
     */
    public function isCurrent();

    /**
     * Whether this menu item is last in its parent
     *
     * @return boolean
     */
    public function isLast();

    /**
     * Whether this menu item is first in its parent
     *
     * @return boolean
     */
    public function isFirst();

    /**
     * Whereas isFirst() returns if this is the first child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the first child that would be rendered
     * for the current user
     *
     * @return boolean
     */
    public function actsLikeFirst();

    /**
     * Whereas isLast() returns if this is the last child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the last child that would be rendered
     * for the current user
     *
     * @return boolean
     */
    public function actsLikeLast();

    /**
     * Calls a method recursively on all of the children of this item
     *
     * @example
     * $menu->callRecursively('setShowChildren', array(false));
     *
     * Provides a fluent interface
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return ItemInterface
     */
    public function callRecursively($method, $arguments = array());

    /**
     * Exports this menu item to an array
     *
     * The children are exported until the specified depth:
     *      null: no limit
     *      0: no children
     *      1: only direct children
     *      ...
     *
     * @param integer $depth
     *
     * @return array
     */
    public function toArray($depth = null);
}
