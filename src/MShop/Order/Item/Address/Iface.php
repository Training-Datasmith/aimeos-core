<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Address;

/**
 * Interface for order address items.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Address\Iface
{
    /**
     * Returns the original customer address ID.
     *
     * @return string Customer address ID
     */
    public function get_address_id(): string;
    /**
     * Sets the original customer address ID.
     *
     * @param string $addrid New customer address ID
     * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
     */
    public function set_address_id(string $addrid): \Aimeos\M_Shop\Order\Item\Address\Iface;
    /**
     * Copys all data from a given address.
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $address New address
     * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Common\Item\Address\Iface $address): \Aimeos\M_Shop\Common\Item\Address\Iface;
}