<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Checks the products in a basket for changed prices
 *
 * Notifies the customers if a price of a product in the basket has changed in
 * the meantime. This plugin can handle the change from net to gross prices and
 * backwards if prices are recalculated for B2B or B2C customers. In these cases
 * the customer won't be notified.
 *
 * The following option is available:
 * - warn: Warn users by displaying a message in the basket if one or more prices
 *   has changed. This can be intentional if the price really has changed but will
 *   also be displayed if the customers get lower block/tier prices or custom prices
 *   after login
 * - ignore-modified: Set to true if all basket items with modified prices (e.g. by
 *   another plugin) should be excluded from the check. Uses the isModified() method
 *   of the item's price object.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Product_Price extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['warn' => ['code' => 'warn', 'internalcode' => 'warn', 'label' => 'Warn customers if price has changed', 'type' => 'bool', 'default' => '0', 'required' => false], 'ignore-modified' => ['code' => 'ignore-modified', 'internalcode' => 'ignore-modified', 'label' => 'Ignore order items with a modified price (e.g. by another plugin)', 'type' => 'bool', 'default' => '1', 'required' => false]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        $errors = parent::check_config_be($attributes);
        return array_merge($errors, $this->check_config($this->be_config, $attributes));
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
     * Subscribes itself to a publisher
     *
     * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $p): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $p->attach($this->object(), 'check.after');
        return $this;
    }
    /**
     * Receives a notification from a publisher object
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        if (!in_array('order/product', (array) $value)) {
            return $value;
        }
        $changed_products = [];
        $attr_ids = $prod_ids = map();
        $order_products = $order->get_products();
        foreach ($order_products as $pos => $item) {
            if ($item->get_flags() & \Aimeos\M_Shop\Order\Item\Product\Base::FLAG_IMMUTABLE || $this->get_config_value('ignore-modified') && $item->get_price()->is_modified()) {
                unset($order_products[$pos]);
            }
            $attr_ids->merge($item->get_attribute_items()->get_attribute_id());
            $prod_ids->push($item->get_parent_product_id())->push($item->get_product_id());
        }
        $attributes = $this->get_attribute_items($attr_ids->unique());
        $products = $this->get_product_items($prod_ids->filter());
        foreach ($order_products as $pos => $order_product) {
            $product = $products->get($order_product->get_product_id());
            $parent = $products->get($order_product->get_parent_product_id());
            if (!$product || !$product->get_ref_items('attribute', 'price', 'custom')->is_empty() || $parent && !$parent->get_ref_items('attribute', 'price', 'custom')->is_empty()) {
                continue;
                // Product isn't available or excluded
            }
            // fetch price of articles/sub-products
            $price = $this->get_price($order_product, $attributes, $this->prices($product, $parent, $pos));
            if ($order_product->get_price()->compare($price) === false) {
                $order->add_product($order_product->set_price($price), $pos);
                $changed_products[$pos] = 'price.changed';
            }
        }
        if ($this->get_config_value('warn', false) == true && count($changed_products) > 0) {
            $code = ['product' => $changed_products];
            $msg = $this->context()->translate('mshop', 'Please have a look at the prices of the products in your basket');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception($msg, -1, null, $code);
        }
        return $value;
    }
    /**
     * Returns the attribute items for the given IDs.
     *
     * @param \Aimeos\Map $list List of attribute IDs
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Attribute\Item\Iface
     */
    protected function get_attribute_items(\Aimeos\Map $list): \Aimeos\Map
    {
        if ($list->is_empty()) {
            return map();
        }
        $attr_manager = \Aimeos\M_Shop::create($this->context(), 'attribute');
        $search = $attr_manager->filter(true)->add(['attribute.id' => $list])->slice(0, count($list));
        return $attr_manager->search($search, ['price']);
    }
    /**
     * Returns the product items for the given product IDs.
     *
     * @param \Aimeos\Map $prodIds Product IDs
     * @return \Aimeos\Map Associative list of IDs as keys and product items as values
     */
    protected function get_product_items(\Aimeos\Map $prod_ids): \Aimeos\Map
    {
        if ($prod_ids->is_empty()) {
            return map();
        }
        $product_manager = \Aimeos\M_Shop::create($this->context(), 'product');
        $search = $product_manager->filter(true)->add(['product.id' => $prod_ids])->slice(0, count($prod_ids));
        $items = $product_manager->search($search, ['catalog', 'price', 'attribute' => ['custom']]);
        return \Aimeos\M_Shop::create($this->context(), 'rule')->apply($items, 'catalog');
    }
    /**
     * Returns the actual price for the given order product.
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface $orderProduct Ordered product
     * @param \Aimeos\Map $attributes Attribute items implementing \Aimeos\MShop\Attribute\Item\Iface with prices
     * @param \Aimeos\Map $prices List of available product prices
     * @return \Aimeos\MShop\Price\Item\Iface Price item including the calculated price
     */
    private function get_price(\Aimeos\M_Shop\Order\Item\Product\Iface $order_product, \Aimeos\Map $attributes, \Aimeos\Map $prices): \Aimeos\M_Shop\Price\Item\Iface
    {
        $site_id = $order_product->get_site_id();
        $currency = $order_product->get_price()->get_currency_id();
        $price_manager = \Aimeos\M_Shop::create($this->context(), 'price');
        $price = clone $price_manager->get_lowest_price($prices, $order_product->get_quantity(), $currency, $site_id);
        // add prices of product attributes to compute the end price for comparison
        foreach ($order_product->get_attribute_items() as $order_attribute) {
            $attr_item = $attributes->get($order_attribute->get_attribute_id());
            $attr_prices = $attr_item ? $attr_item->get_ref_items('price', 'default', 'default') : map();
            if (!$attr_prices->is_empty()) {
                $low_price = $price_manager->get_lowest_price($attr_prices, $order_attribute->get_quantity(), $currency, $site_id);
                $price = $price->add_item($low_price, $order_attribute->get_quantity());
            }
        }
        // reset product rebates like in the basket controller
        return $price->set_rebate('0.00');
    }
    /**
     * Returns the available prices for the ordered product
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product with prices
     * @param \Aimeos\MShop\Product\Item\Iface|null $parent Parent product with prices on NULL if no parent is available
     * @param int $pos Position of the product in the basket
     * @return \Aimeos\Map List of available product prices
     */
    protected function prices(\Aimeos\M_Shop\Product\Item\Iface $product, ?\Aimeos\M_Shop\Product\Item\Iface $parent, int $pos): \Aimeos\Map
    {
        $prices = $product->get_ref_items('price', 'default', 'default');
        // fetch prices of selection/parent products
        if ($parent && $prices->is_empty()) {
            $prices = $parent->get_ref_items('price', 'default', 'default');
        }
        if ($prices->is_empty()) {
            $codes = ['product' => [$pos => 'product.price']];
            $msg = $this->context()->translate('mshop', 'No price for product available');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception($msg, -1, null, $codes);
        }
        return $prices;
    }
}