<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025-2026
 * @package MShop
 * @subpackage Product
 */
namespace Aimeos\M_Shop\Product\Item;

/**
 * Stock trait for products containing stock items
 *
 * @package MShop
 * @subpackage Product
 */
trait Stock
{
    private array $stock_items = [];
    private array $stock_rm_items = [];
    /**
     * Returns the unique ID of the item.
     *
     * @return string|null ID of the item
     */
    abstract public function get_id(): ?string;
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        parent::__clone();
        foreach ($this->stock_items as $key => $item) {
            $this->stock_items[$key] = clone $item;
        }
        foreach ($this->stock_rm_items as $key => $item) {
            $this->stock_rm_items[$key] = clone $item;
        }
    }
    /**
     * Adds a new stock item or overwrite an existing one
     *
     * @param \Aimeos\MShop\Stock\Item\Iface $item New or existing stock item
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function add_stock_item(\Aimeos\M_Shop\Stock\Item\Iface $item): \Aimeos\M_Shop\Product\Item\Iface
    {
        $id = $item->get_id() ?: '_' . $this->get_id() . '_' . $item->get_type();
        $this->stock_items[$id] = $item;
        return $this;
    }
    /**
     * Adds new stock items or overwrite existing ones
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface $item New or existing stock item
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function add_stock_items(iterable $items): \Aimeos\M_Shop\Product\Item\Iface
    {
        foreach ($items as $item) {
            $this->add_stock_item($item);
        }
        return $this;
    }
    /**
     * Removes an existing stock item
     *
     * @param \Aimeos\MShop\Stock\Item\Iface $item Existing stock item
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function delete_stock_item(\Aimeos\M_Shop\Stock\Item\Iface $item): \Aimeos\M_Shop\Product\Item\Iface
    {
        $id = $item->get_id();
        if (isset($this->stock_items[$id])) {
            $this->stock_rm_items[$id] = $item;
            unset($this->stock_items[$id]);
            return $this;
        }
        $id = '_' . $this->get_id() . '_' . $item->get_type();
        if (isset($this->stock_items[$id])) {
            $this->stock_rm_items[$id] = $item;
            unset($this->stock_items[$id]);
        }
        return $this;
    }
    /**
     * Removes a list of existing stock items
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface[] $items Existing stock items
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function delete_stock_items(iterable $items): \Aimeos\M_Shop\Product\Item\Iface
    {
        foreach ($items as $item) {
            $this->delete_stock_item($item);
        }
        return $this;
    }
    /**
     * Returns the deleted stock items
     *
     * @return \Aimeos\Map Stock items implementing \Aimeos\MShop\Stock\Item\Iface
     */
    public function get_stock_items_deleted(): \Aimeos\Map
    {
        return map($this->stock_rm_items);
    }
    /**
     * Returns the stock items associated to the product
     *
     * @param array|string|null $type Type or types of the stock item
     * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Stock\Item\Iface
     */
    public function get_stock_items($type = null): \Aimeos\Map
    {
        $list = map($this->stock_items);
        if ($type !== null) {
            return $list->filter(fn($item) => in_array($item->get_type(), (array) $type, true));
        }
        return $list;
    }
    /**
     * Adds a new stock item or overwrite an existing one
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface[] $items New list of stock items
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function set_stock_items(iterable $items): \Aimeos\M_Shop\Product\Item\Iface
    {
        $list = [];
        foreach ($items as $p) {
            $id = $p->get_id() ?: '_' . $this->get_id() . '_' . $p->get_type();
            unset($this->stock_items[$id]);
            $list[$id] = $p;
        }
        $this->delete_stock_items($this->stock_items);
        $this->stock_items = $list;
        return $this;
    }
    /**
     * Sets the stock items in the trait
     *
     * @param \Aimeos\MShop\Stock\Item\Iface[] $items Stock items
     */
    protected function init_stock_items(array $items)
    {
        $this->stock_items = $items;
    }
}