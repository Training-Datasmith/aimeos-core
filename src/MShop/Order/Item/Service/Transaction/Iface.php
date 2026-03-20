<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Service\Transaction;

/**
 * Interface for order service transaction item
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Parentid\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Sets the site ID of the item.
     *
     * @param string $value Unique site ID of the item
     * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service attribute item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Service\Transaction\Iface;
    /**
     * Returns the price object which belongs to the service item.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item
     */
    public function get_price(): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Sets a new price object for the service item.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $price Price item
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_price(\Aimeos\M_Shop\Price\Item\Iface $price): \Aimeos\M_Shop\Order\Item\Service\Transaction\Iface;
}