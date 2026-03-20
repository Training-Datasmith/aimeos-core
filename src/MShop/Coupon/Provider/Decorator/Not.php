<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider\Decorator;

/**
 * Negation decorator for coupon providers
 *
 * @package MShop
 * @subpackage Coupon
 */
class Not extends \Aimeos\M_Shop\Coupon\Provider\Decorator\Base implements \Aimeos\M_Shop\Coupon\Provider\Decorator\Iface
{
    /**
     * Tests if a coupon should be granted
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @return bool True if available, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $order): bool
    {
        return !$this->get_provider()->is_available($order);
    }
}