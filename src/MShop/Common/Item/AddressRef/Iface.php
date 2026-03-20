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
 * Common interface for items containing address items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface
{
    /**
     * Adds a new address item or overwrite an existing one
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $item New or existing address item
     * @param string|null $key Key in the list of address items or null to add the item at the end
     * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
     */
    public function add_address_item(\Aimeos\M_Shop\Common\Item\Address\Iface $item, ?string $key = null): \Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Removes an existing address item
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $item Existing address item
     * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
     */
    public function delete_address_item(\Aimeos\M_Shop\Common\Item\Address\Iface $item): \Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Removes a list of existing address items
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Address\Iface[] $items Existing address items
     * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
     */
    public function delete_address_items(iterable $items): \Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Returns the deleted address items
     *
     * @return \Aimeos\Map Address items implementing \Aimeos\MShop\Common\Item\Address\Iface
     */
    public function get_address_items_deleted(): \Aimeos\Map;
    /**
     * Returns the address items
     *
     * @param string $key Key in the list of address items
     * @return \Aimeos\MShop\Common\Item\Address\Iface|null Address item or null if not found
     */
    public function get_address_item(string $key): ?\Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Returns the address items
     *
     * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Common\Item\Address\Iface
     */
    public function get_address_items(): \Aimeos\Map;
}