<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Defines the number of the free "config" attributes for products with configurable options
 *
 * If customers can configure products using several options they can add and each option must be paid additionally,
 * this plugin recalculates the total product price based on the added options.
 *
 * Example:
 *  <attribute type> : <number of free options>
 *
 * @package MShop
 * @subpackage Plugin
 */
class Product_Free_Options extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    /**
     * Subscribes itself to a publisher
     *
     * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $p): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $plugin = $this->object();
        $p->attach($plugin, 'addProduct.after');
        $p->attach($plugin, 'setProducts.after');
        return $this;
    }
    /**
     * Receives a notification from a publisher object
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null): \Aimeos\M_Shop\Order\Item\Product\Iface|array
    {
        if (is_array($value)) {
            foreach ($value as $key => $product) {
                $value[$key] = $this->update_price($product);
            }
        } else {
            $value = $this->update_price($value);
        }
        return $value;
    }
    /**
     * Adds the prices of the attribute items without the given amount of free items
     *
     * @param \Aimeos\MShop\Price\Item\Iface $price Product price item
     * @param array $attrItems Associative list of attribute IDs as keys and items with prices as values
     * @param array $quantities Associative list of attribute IDs as keys and their quantities as values
     * @param int $free Number of free items
     * @return \Aimeos\MShop\Price\Item\Iface Price item with attribute prices added
     */
    protected function add_prices(\Aimeos\M_Shop\Price\Item\Iface $price, array $attr_items, array $quantities, int $free): \Aimeos\M_Shop\Price\Item\Iface
    {
        $price_manager = \Aimeos\M_Shop::create($this->context(), 'price');
        foreach ($attr_items as $attr_id => $attr_item) {
            $prices = $attr_item->get_ref_items('price', 'default', 'default');
            if (!$prices->is_empty()) {
                $qty = $quantities[$attr_id] ?? 0;
                $quantity = $qty >= $free ? $qty - $free : 0;
                $free = $free >= $qty ? $free - $qty : 0;
                if ($quantity > 0) {
                    $price_item = $price_manager->get_lowest_price($prices, $quantity);
                    $price = $price->add_item($price_item, $quantity);
                }
            }
        }
        return $price;
    }
    /**
     * Returns the attribute items including the prices for the given IDs
     *
     * @param array $ids List of attribute IDs
     * @return array Associative List of attribute type and ID as keys and \Aimeos\MShop\Attribute\Item\Iface as values
     */
    protected function get_attribute_map(array $ids): array
    {
        $attr_map = [];
        $attr_manager = \Aimeos\M_Shop::create($this->context(), 'attribute');
        $search = $attr_manager->filter()->slice(0, count($ids));
        $search->set_conditions($search->compare('==', 'attribute.id', $ids));
        foreach ($attr_manager->search($search, ['price']) as $attr_id => $attr_item) {
            $attr_map[$attr_item->get_type()][$attr_id] = $attr_item;
        }
        return $attr_map;
    }
    /**
     * Sorts the given attribute items by their price (lowest first)
     *
     * @param \Aimeos\MShop\Attribute\Item\Iface[] $attrItems Associative list of attribute IDs as keys and items as values
     * @param array $attrQtys Associative list of attribute IDs as keys and their quantities as values
     * @return \Aimeos\MShop\Attribute\Item\Iface[] Sorted associative list of attribute IDs as keys and items as values
     */
    protected function sort_by_price(array $attr_items, array $attr_qtys): array
    {
        $price_manager = \Aimeos\M_Shop::create($this->context(), 'price');
        $sort_fcn = function ($a, $b) use ($price_manager, $attr_qtys): int {
            if (($prices_a = $a->get_ref_items('price', 'default', 'default')->to_array()) === []) {
                return 1;
            }
            if (($prices_b = $b->get_ref_items('price', 'default', 'default')->to_array()) === []) {
                return -1;
            }
            $qty = $attr_qtys[$a->get_id()] ?? 0;
            $p1 = $price_manager->get_lowest_price($prices_a, $qty);
            $qty = $attr_qtys[$b->get_id()] ?? 0;
            $p2 = $price_manager->get_lowest_price($prices_b, $qty);
            if ($p1->get_value() < $p2->get_value()) {
                return -1;
            }
            if ($p1->get_value() > $p2->get_value()) {
                return 1;
            }
            return 0;
        };
        uasort($attr_items, $sort_fcn);
        return $attr_items;
    }
    /** Updates the price of the product
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface $product Ordered product for updating the price
     * @return \Aimeos\MShop\Order\Item\Product\Iface Ordered product with updated price
     */
    protected function update_price(\Aimeos\M_Shop\Order\Item\Product\Iface $product): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        $attr_qtys = $attr_types = [];
        $context = $this->context();
        $prod_item = \Aimeos\M_Shop::create($context, 'product')->get($product->get_product_id(), ['price']);
        $prod_conf = $prod_item->get_config();
        foreach ($product->get_attribute_items('config') as $attr) {
            $attr_qtys[$attr->get_attribute_id()] = $attr->get_quantity();
            $attr_types[] = $attr->get_code();
        }
        if (array_intersect($attr_types, array_keys($prod_conf)) === []) {
            return $product;
        }
        $prices = $prod_item->get_ref_items('price', 'default', 'default');
        $price_item = \Aimeos\M_Shop::create($context, 'price')->get_lowest_price($prices, $product->get_quantity());
        foreach ($this->get_attribute_map(array_keys($attr_qtys)) as $type => $list) {
            if (isset($prod_conf[$type])) {
                $list = $this->sort_by_price($list, $attr_qtys);
                $price_item = $this->add_prices($price_item, $list, $attr_qtys, (int) $prod_conf[$type]);
            } else {
                $price_item = $this->add_prices($price_item, $list, $attr_qtys, 0);
            }
        }
        return $product->set_price($price_item);
    }
}