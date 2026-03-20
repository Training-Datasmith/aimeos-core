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
 * Checks the current availability of the products in a basket
 *
 * Products can be removed or disabled by the shop owner or the time frame a
 * product is avialable can pass by. In these cases, the plugin notifies the
 * customers that they have to remove the product from the basket before they
 * can proceed in the checkout process.
 *
 * The plugin is executed for the basket and the checkout summary page.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Product_Gone extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
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
        $not_available = [];
        $product_ids = $order->get_products()->get_product_id()->to_array();
        $product_manager = \Aimeos\M_Shop::create($this->context(), 'product');
        $filter = $product_manager->filter(true)->add('product.id', '==', $product_ids);
        $check_items = $product_manager->search($filter);
        foreach ($order->get_products() as $position => $order_product) {
            if (($product = $check_items->get($order_product->get_product_id())) === null) {
                $not_available[$position] = 'gone.notexist';
                continue;
            }
        }
        if (count($not_available) > 0) {
            $code = ['product' => $not_available];
            $msg = $this->context()->translate('mshop', 'Products in basket not available');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception($msg, -1, null, $code);
        }
        return $value;
    }
}