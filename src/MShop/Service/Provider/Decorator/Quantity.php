<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Decorator for adding quantity based costs
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Quantity extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['quantity.packagesize' => ['code' => 'quantity.packagesize', 'internalcode' => 'quantity.packagesize', 'label' => 'Number of products in the package', 'type' => 'number', 'default' => '1', 'required' => false], 'quantity.packagecosts' => ['code' => 'quantity.packagecosts', 'internalcode' => 'quantity.packagecosts', 'label' => 'Costs per the package', 'type' => 'number', 'default' => '', 'required' => true]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     *    known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        $error = $this->get_provider()->check_config_be($attributes);
        return $error + $this->check_config($this->be_config, $attributes);
    }
    /**
     * Returns the configuration attribute definitions of the provider
     *
     * This will generate a list of available fields and rules for the value of
     * each field in the administration interface.
     *
     * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
     */
    public function get_config_be(): array
    {
        return array_replace(parent::get_config_be(), $this->get_config_items($this->be_config));
    }
    /**
     * Returns the price when using the provider.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @param array $options Selected options by customer from frontend
     * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
     */
    public function calc_price(\Aimeos\M_Shop\Order\Item\Iface $basket, array $options = []): \Aimeos\M_Shop\Price\Item\Iface
    {
        $sum = 0;
        $price = $this->get_provider()->calc_price($basket, $options);
        foreach ($basket->get_products() as $order_product) {
            $qty = $order_product->get_quantity();
            if (!($products = $order_product->get_products())->is_empty()) {
                foreach ($products as $prod_item) {
                    // calculate bundled products
                    $sum += $qty * $prod_item->get_quantity();
                }
            } else {
                $sum += $qty;
            }
        }
        $size = $this->get_config_value(['quantity.packagesize'], 1);
        $costs = $this->get_config_value(['quantity.packagecosts'], 0.0);
        $value = ceil($sum / $size) * $costs;
        return $price->set_costs($price->get_costs() + $value);
    }
}