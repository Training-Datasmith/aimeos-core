<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider\Decorator;

/**
 * Base decorator methods for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class Base extends \Aimeos\M_Shop\Coupon\Provider\Base
{
    /**
     * Initializes a new coupon provider object using the given context object.
     *
     * @param \Aimeos\MShop\Coupon\Provider\Iface $provider Coupon provider interface
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Coupon\Item\Iface $couponItem Coupon item with configuration for the provider
     * @param string $code Coupon code entered by the customer
     */
    public function __construct(private \Aimeos\M_Shop\Coupon\Provider\Iface $provider, \Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Coupon\Item\Iface $coupon_item, string $code)
    {
        parent::__construct($context, $coupon_item, $code);
    }
    /**
     * Returns the price the discount should be applied to
     *
     * The result depends on the configured restrictions and it must be less or
     * equal to the passed price.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return \Aimeos\MShop\Price\Item\Iface New price that should be used
     */
    public function calc_price(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->provider->calc_price($order);
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return $this->provider->get_config_be();
    }
    /**
     * Updates the result of a coupon to the order base instance.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        if ($this->object()->is_available($order)) {
            $this->provider->update($order);
        } else {
            $order->set_coupon($this->get_code(), []);
        }
        return $this;
    }
    /**
     * Tests if a coupon should be granted.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return bool True of coupon can be granted, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $order): bool
    {
        return $this->provider->is_available($order);
    }
    /**
     * Injects the reference of the outmost object
     *
     * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outmost provider or decorator
     * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon object for chaining method calls
     */
    public function set_object(\Aimeos\M_Shop\Coupon\Provider\Iface $object): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        parent::set_object($object);
        $this->provider->set_object($object);
        return $this;
    }
    /**
     * Returns the stored provider object.
     *
     * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon provider
     */
    protected function get_provider(): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        return $this->provider;
    }
}