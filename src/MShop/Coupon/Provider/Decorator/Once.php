<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider\Decorator;

/**
 * Decorator to allow using a coupon by a customer only once
 *
 * @package MShop
 * @subpackage Coupon
 */
class Once extends \Aimeos\M_Shop\Coupon\Provider\Decorator\Base implements \Aimeos\M_Shop\Coupon\Provider\Decorator\Iface
{
    /**
     * Checks for requirements.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return bool True if the requirements are met, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $order): bool
    {
        $addresses = $order->get_address(\Aimeos\M_Shop\Order\Item\Address\Base::TYPE_PAYMENT);
        if (($address = reset($addresses)) !== false) {
            $manager = \Aimeos\M_Shop::create($this->context(), 'order');
            $search = $manager->filter()->slice(0, 1);
            $expr = [$search->compare('==', 'order.address.email', $address->get_email()), $search->compare('==', 'order.coupon.code', $this->get_code()), $search->compare('>=', 'order.statuspayment', \Aimeos\M_Shop\Order\Item\Base::PAY_PENDING)];
            $search->set_conditions($search->and($expr));
            if (!$manager->search($search)->is_empty()) {
                return false;
            }
        }
        return parent::is_available($order);
    }
}