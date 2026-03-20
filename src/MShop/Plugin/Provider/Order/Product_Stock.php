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
 * Checks the products in a basket for sufficient stocklevel
 *
 * Notifies the customers if one or more products have gone out of stock in the
 * meantime. They have to remove this products before they can continue in the
 * checkout process.
 *
 * Also, the plugin reduces the product quantity automatically if there are not
 * enough products in stock.
 *
 * The checks are executed for the basket and checkout summary view.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Product_Stock extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    /**
     * Subscribes itself to a publisher
     *
     * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $p): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $p->attach($this->object(), 'addProduct.after');
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
        if (!$order->get_products()->is_empty() && ($out_of_stock = $this->check_stock($order)) !== []) {
            $msg = $this->context()->translate('mshop', 'Products out of stock');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception($msg, -1, null, ['product' => $out_of_stock]);
        }
        return $value;
    }
    /**
     * Checks if all products in the basket have enough stock
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket object
     * @return array Associative list of basket product positions as keys and the error codes as values
     */
    protected function check_stock(\Aimeos\M_Shop\Order\Item\Iface $order): array
    {
        $context = $this->context();
        $site_ids = $context->locale()->get_site_path();
        $manager = \Aimeos\M_Shop::create($context, 'stock');
        $filter = $manager->filter();
        $expr = $stock_map = [];
        foreach ($order->get_products() as $order_product) {
            $expr[] = $filter->and([
                // use stocks from parent sites if none for the site the product is from is available
                $filter->is('stock.siteid', '==', array_merge($site_ids, [$order_product->get_site_id()])),
                $filter->is('stock.productid', '==', $order_product->get_product_id()),
                $filter->is('stock.type', '==', $order_product->get_stock_type()),
            ]);
        }
        $filter->add($filter->or($expr))->slice(0, 0x7fffffff);
        foreach ($manager->search($filter) as $item) {
            $stock_map[$item->get_site_id()][$item->get_product_id()][$item->get_type()] = $item;
        }
        return $this->check_stock_levels($order, $stock_map);
    }
    /**
     * Checks if the products in the basket have enough stock
     *
     * Removes products from the basket which are out of stock and decreases the
     * quantities of orders products if there's not enough stock.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket object
     * @param array $stockMap Multi-dimensional associative list of product ID / stock type as keys and stock level as values
     * @return array Associative list of basket positions as keys and error codes as values
     */
    protected function check_stock_levels(\Aimeos\M_Shop\Order\Item\Iface $order, array $stock_map): array
    {
        $out_of_stock = [];
        $products = $order->get_products();
        $site_ids = $this->context()->locale()->get_site_path();
        foreach ($products as $pos => $order_product) {
            $stocklevel = 0;
            $type = $order_product->get_stock_type();
            $prodid = $order_product->get_product_id();
            foreach (array_merge($site_ids, [$order_product->get_site_id()]) as $siteid) {
                if (isset($stock_map[$siteid][$prodid][$type])) {
                    $stock_item = $stock_map[$siteid][$prodid][$type];
                    $order_product->set_time_frame($stock_item->get_time_frame());
                    if (($stocklevel = $stock_item->get_stock_level()) === null) {
                        continue 2;
                    }
                    if ($stocklevel >= $order_product->get_quantity()) {
                        $stock = $stock_item->get_stock_level() - $order_product->get_quantity();
                        $stock_item->set_stock_level($stock);
                        continue 2;
                    }
                }
            }
            if ($stocklevel > 0) {
                // update quantity to actual stock level
                $order->add_product($order_product->set_quantity($stocklevel), $pos);
            } else {
                $order->delete_product($pos);
            }
            $out_of_stock[$pos] = 'stock.notenough';
        }
        return $out_of_stock;
    }
}