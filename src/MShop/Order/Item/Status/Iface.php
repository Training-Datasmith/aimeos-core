<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Status;

/**
 * Generic interface for order status.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Parentid\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the value of the order status.
     *
     * @return string Value of the order status
     */
    public function get_value(): string;
    /**
     * Sets the value of the order status.
     *
     * @param string $value Value of the order status
     * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
     */
    public function set_value(string $value): \Aimeos\M_Shop\Order\Item\Status\Iface;
}