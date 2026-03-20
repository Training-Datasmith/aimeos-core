<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Address_Ref;

/**
 * Common trait for items containing address items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
    private int $addr_max = 0;
    private array $addr_items = [];
    private array $addr_rm_items = [];
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        parent::__clone();
        foreach ($this->addr_items as $key => $item) {
            $this->addr_items[$key] = clone $item;
        }
        foreach ($this->addr_rm_items as $key => $item) {
            $this->addr_rm_items[$key] = clone $item;
        }
    }
    /**
     * Adds a new address item or overwrite an existing one
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $item New or existing address item
     * @param string|null $key Key in the list of address items or null to add the item at the end
     * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
     */
    public function add_address_item(\Aimeos\M_Shop\Common\Item\Address\Iface $item, ?string $key = null): \Aimeos\M_Shop\Common\Item\Iface
    {
        $key !== null ? $this->addr_items[$key] = $item : $this->addr_items[] = $item;
        return $this;
    }
    /**
     * Removes an existing address item
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $item Existing address item
     * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
     */
    public function delete_address_item(\Aimeos\M_Shop\Common\Item\Address\Iface $item): \Aimeos\M_Shop\Common\Item\Iface
    {
        foreach ($this->addr_items as $key => $addr_item) {
            if ($addr_item === $item) {
                $this->addr_rm_items[$item->get_id()] = $item;
                unset($this->addr_items[$key]);
                return $this;
            }
        }
        return $this;
    }
    /**
     * Removes a list of existing address items
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Address\Iface[] $items Existing address items
     * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
     * @throws \Aimeos\MShop\Exception If an item isn't a address item or isn't found
     */
    public function delete_address_items(iterable $items): \Aimeos\M_Shop\Common\Item\Iface
    {
        foreach ($items as $item) {
            $this->delete_address_item($item);
        }
        return $this;
    }
    /**
     * Returns the deleted address items
     *
     * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Common\Item\Address\Iface
     */
    public function get_address_items_deleted(): \Aimeos\Map
    {
        return map($this->addr_rm_items);
    }
    /**
     * Returns the address items
     *
     * @param string $key Key in the list of address items
     * @return \Aimeos\MShop\Common\Item\Address\Iface|null Address item or null if not found
     */
    public function get_address_item(string $key): ?\Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->addr_items[$key] ?? null;
    }
    /**
     * Returns the address items
     *
     * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Common\Item\Address\Iface
     */
    public function get_address_items(): \Aimeos\Map
    {
        return map($this->addr_items);
    }
    /**
     * Initializes the address items in the trait
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface[] $items Address items
     */
    protected function init_address_items(array $items)
    {
        $this->addr_items = $items;
    }
}