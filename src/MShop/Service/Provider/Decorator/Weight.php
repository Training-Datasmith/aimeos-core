<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Decorator for service providers adding additional costs
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Weight extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['weight.min' => ['code' => 'weight.min', 'internalcode' => 'weight.min', 'label' => 'Minimum weight of the package', 'type' => 'number', 'default' => '', 'required' => false], 'weight.max' => ['code' => 'weight.max', 'internalcode' => 'weight.max', 'label' => 'Maximum weight of the package', 'type' => 'number', 'default' => '', 'required' => false]];
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
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return array_replace(parent::get_config_be(), $this->get_config_items($this->be_config));
    }
    /**
     * Checks if the the basket weight is ok for the service provider.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        if ($this->check_weight_scale($this->get_weight($this->get_quantities($basket))) === false) {
            return false;
        }
        return $this->get_provider()->is_available($basket);
    }
    /**
     * Checks if the country code is in the list of codes specified by the given key
     *
     * @param float $basketWeight The basket weight
     * @return bool True if the current basket weight is within the providers weight range
     */
    protected function check_weight_scale(float $basket_weight): bool
    {
        $min = $this->get_config_value(['weight.min']);
        $max = $this->get_config_value(['weight.max']);
        if ($min !== null && (float) $min > $basket_weight) {
            return false;
        }
        if ($max !== null && (float) $max < $basket_weight) {
            return false;
        }
        return true;
    }
    /**
     * Returns the product quantities
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return array Associative list of product codes as keys and quantities as values
     */
    protected function get_quantities(\Aimeos\M_Shop\Order\Item\Iface $basket): array
    {
        $prod_map = [];
        // basket can contain a product several times in different basket items
        foreach ($basket->get_products() as $order_product) {
            $code = $order_product->get_product_code();
            $prod_map[$code] = ($prod_map[$code] ?? 0) + $order_product->get_quantity();
            foreach ($order_product->get_products() as $prod_item) {
                // calculate bundled products
                $code = $prod_item->get_product_code();
                $prod_map[$code] = ($prod_map[$code] ?? 0) + $prod_item->get_quantity();
            }
        }
        return $prod_map;
    }
    /**
     * Returns the weight of the products
     *
     * @param array $prodMap Associative list of product codes as keys and quantities as values
     * @return float Sumed up product weight multiplied with its quantity
     */
    protected function get_weight(array $prod_map): float
    {
        $weight = 0;
        $manager = \Aimeos\M_Shop::create($this->context(), 'product');
        $search = $manager->filter()->add(['product.code' => array_keys($prod_map)])->slice(0, count($prod_map));
        foreach ($manager->search($search, ['product/property' => ['package-weight']]) as $product) {
            foreach ($product->get_properties('package-weight') as $value) {
                $weight += $value * $prod_map[$product->get_code()];
            }
        }
        return $weight;
    }
}