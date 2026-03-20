<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider;

/**
 * No coupon model
 *
 * @package MShop
 * @subpackage Coupon
 */
class None extends \Aimeos\M_Shop\Coupon\Provider\Factory\Base implements \Aimeos\M_Shop\Coupon\Provider\Iface, \Aimeos\M_Shop\Coupon\Provider\Factory\Iface
{
    /**
     * Updates the result of a coupon to the order base instance.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        $order->set_coupon($this->get_code(), []);
        return $this;
    }
}