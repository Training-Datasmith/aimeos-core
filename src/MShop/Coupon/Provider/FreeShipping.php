<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider;

/**
 * Free shipping coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Free_Shipping extends \Aimeos\M_Shop\Coupon\Provider\Factory\Base implements \Aimeos\M_Shop\Coupon\Provider\Iface, \Aimeos\M_Shop\Coupon\Provider\Factory\Iface
{
    private array $be_config = ['freeshipping.productcode' => ['code' => 'freeshipping.productcode', 'internalcode' => 'freeshipping.productcode', 'label' => 'Product code of the free shipping product', 'default' => '', 'required' => true]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        return $this->check_config($this->be_config, $attributes);
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return $this->get_config_items($this->be_config);
    }
    /**
     * Updates the result of a coupon to the order base instance.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        if (($prodcode = $this->get_config_value('freeshipping.productcode')) === null) {
            $msg = $this->context()->translate('mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"');
            $msg = sprintf($msg, $this->get_item()->get_provider(), 'freeshipping.productcode');
            throw new \Aimeos\M_Shop\Coupon\Exception($msg);
        }
        $order_product = $this->create_product($prodcode);
        $price = $order_product->get_price()->clear();
        foreach ($order->get_service(\Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY) as $service) {
            $price = $price->set_rebate($price->get_rebate() + $service->get_price()->get_costs())->set_costs($price->get_costs() - $service->get_price()->get_costs())->set_tax_rates($service->get_price()->get_tax_rates());
        }
        $order->set_coupon($this->get_code(), [$order_product->set_price($price)]);
        return $this;
    }
}