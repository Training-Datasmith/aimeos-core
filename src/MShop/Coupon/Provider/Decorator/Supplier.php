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
 * Decorator for restricting coupons to suppliers
 *
 * @package MShop
 * @subpackage Coupon
 */
class Supplier extends \Aimeos\M_Shop\Coupon\Provider\Decorator\Base implements \Aimeos\M_Shop\Coupon\Provider\Decorator\Iface
{
    private array $be_config = ['supplier.code' => ['code' => 'supplier.code', 'internalcode' => 'supplier.code', 'label' => 'Comma separated supplier codes', 'default' => '', 'required' => true], 'supplier.only' => ['code' => 'supplier.only', 'internalcode' => 'supplier.only', 'label' => 'Rebate is applied only to products of that supplier', 'type' => 'bool', 'default' => false, 'required' => false]];
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
        if ($this->get_config_value('supplier.only') == true) {
            $prod_ids = [];
            $context = $this->context();
            $types = ['default', 'promotion'];
            $cat_manager = \Aimeos\M_Shop::create($context, 'supplier');
            $prod_manager = \Aimeos\M_Shop::create($context, 'product');
            $codes = explode(',', $this->get_config_value('supplier.code', ''));
            $filter = $cat_manager->filter(true)->add(['supplier.code' => $codes])->slice(0, count($codes));
            $cat_ids = $cat_manager->search($filter)->keys()->all();
            $price = \Aimeos\M_Shop::create($context, 'price')->create();
            foreach ($order->get_products() as $product) {
                $prod_ids[$product->get_product_id()][] = $product;
                if ($parentid = $product->get_parent_product_id()) {
                    $prod_ids[$parentid][] = $product;
                }
            }
            $filter = $prod_manager->filter(true)->slice(0, count($prod_ids));
            $filter->add($filter->is($filter->make('product:has', ['supplier', $types, $cat_ids]), '!=', null))->add($filter->is('product.id', '==', array_keys($prod_ids)));
            foreach ($prod_manager->search($filter) as $item) {
                foreach ($prod_ids[$item->get_id()] ?? [] as $product) {
                    $price = $price->add_item($product->get_price(), $product->get_quantity());
                }
            }
            return $price;
        }
        return $this->get_provider()->calc_price($order);
    }
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
        return array_replace(parent::get_config_be(), $this->get_config_items($this->be_config));
    }
    /**
     * Checks for requirements.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return bool True if the requirements are met, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $order): bool
    {
        if (($codes = $this->get_config_value('supplier.code')) !== null) {
            $expr = [];
            $context = $this->context();
            $types = ['default', 'promotion'];
            $cat_manager = \Aimeos\M_Shop::create($context, 'supplier');
            $prod_manager = \Aimeos\M_Shop::create($context, 'product');
            $filter = $cat_manager->filter(true)->add(['supplier.code' => explode(',', $codes)]);
            $cat_ids = $cat_manager->search($filter)->keys()->all();
            $filter = $prod_manager->filter(true);
            foreach ($order->get_products() as $product) {
                $expr[] = $filter->is($filter->make('product:has', ['supplier', $types, $cat_ids]), '!=', null);
            }
            if ($prod_manager->search($filter->add($filter->or($expr)))->is_empty()) {
                return false;
            }
        }
        return parent::is_available($order);
    }
}