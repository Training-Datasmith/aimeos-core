<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Tree;

/**
 * Common interface for tree items
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the code of the item.
     *
     * @return string Code of the item
     */
    public function get_code(): string;
    /**
     * Sets the code of the item.
     *
     * @param string $name New code of the item
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Item for chaining method calls
     */
    public function set_code(string $name): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Returns the name of the item.
     *
     * @return string Name of the item
     */
    public function get_label(): string;
    /**
     * Sets the new name of the item.
     *
     * @param string $name New name of the item
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Item for chaining method calls
     */
    public function set_label(string $name): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Returns the level of the item in the tree
     *
     * @return int Level of the item starting with "0" for the root node
     */
    public function get_level(): int;
    /**
     * Returns the ID of the parent category
     *
     * @return string|null Unique ID of the parent category
     */
    public function get_parent_id(): ?string;
    /**
     * Adds a child node to this node.
     *
     * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Catalog item for chaining method calls
     */
    public function add_child(\Aimeos\M_Shop\Common\Item\Tree\Iface $item): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Removes a child node from this node.
     *
     * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to remove
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
     */
    public function delete_child(\Aimeos\M_Shop\Common\Item\Tree\Iface $item): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Returns a child of this node identified by its index.
     *
     * @param int $index Index of child node
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Selected node
     */
    public function get_child(int $index): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Returns all children of this node.
     *
     * @return \Aimeos\Map Numerically indexed list of items implementing \Aimeos\MShop\Common\Item\Tree\Iface
     */
    public function get_children(): \Aimeos\Map;
    /**
     * Returns the deleted children.
     *
     * @return \Aimeos\Map List of removed children implementing \Aimeos\MShop\Common\Item\Tree\Iface
     */
    public function get_children_deleted(): \Aimeos\Map;
    /**
     * Tests if a node has children.
     *
     * @return bool True if node has children, false if not
     */
    public function has_children(): bool;
    /**
     * Returns the node and its children as list
     *
     * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Common\Item\Tree\Iface
     */
    public function to_list(): \Aimeos\Map;
}