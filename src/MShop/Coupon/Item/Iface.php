<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Item;

/**
 * Generic interface for coupons created and saved by the coupon managers.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Time\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the label of the coupon if available.
     *
     * @return string Name/label of the coupon item
     */
    public function get_label(): string;
    /**
     * Sets the label of the coupon item.
     *
     * @param string $name Name/label of the coupon item.
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function set_label(string $name): \Aimeos\M_Shop\Coupon\Item\Iface;
    /**
     * Returns the provider of the coupon.
     *
     * @return string Name of the provider which is the short provider class name
     */
    public function get_provider(): string;
    /**
     * Sets the new provider of the coupon item which is the short name of the provider class name.
     *
     * @param string $provider Coupon provider, esp. short provider class name
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function set_provider(string $provider): \Aimeos\M_Shop\Coupon\Item\Iface;
}