<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Updates service items on basket change
 *
 * Delivery or payment service options can be restricted to certain requirements
 * like the basket value, the delivery address or if virtual (download) products
 * are in the basket. If the service option is not available any more due to one
 * of these restrictions, it will be removed from the basket. Otherwise, the
 * price of the service option is recalculated.
 *
 * This plugin interacts with the "Autofill" plugin, which may re-add one of the
 * other delivery/payment options automatically, that are still available!
 *
 * @package MShop
 * @subpackage Plugin
 */
class Services_Update extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
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
        $p->attach($plugin, 'addAddress.after');
        $p->attach($plugin, 'deleteAddress.after');
        $p->attach($plugin, 'setAddresses.after');
        $p->attach($plugin, 'addCoupon.after');
        $p->attach($plugin, 'deleteCoupon.after');
        $p->attach($plugin, 'addProduct.after');
        $p->attach($plugin, 'deleteProduct.after');
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
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        $services = $order->get_services();
        if ($order->get_products()->is_empty()) {
            $price_manager = \Aimeos\M_Shop::create($this->context(), 'price');
            foreach ($services as $type => $list) {
                $service_items = $list;
                foreach ($list as $key => $item) {
                    $service_items[$key] = $item->set_price($price_manager->create());
                }
                $services[$type] = $service_items;
            }
            $order->set_services($services->to_array());
            return $value;
        }
        $service_items = $this->get_service_items($services);
        $service_manager = \Aimeos\M_Shop::create($this->context(), 'service');
        foreach ($services as $type => $list) {
            $order_services = $list;
            foreach ($list as $key => $item) {
                if (($service_item = $service_items->get($item->get_service_id())) !== null) {
                    $provider = $service_manager->get_provider($service_item, $service_item->get_type());
                    if ($provider->is_available($order)) {
                        $order_services[$key] = $item->set_price($provider->calc_price($order));
                        continue;
                    }
                }
                unset($order_services[$key]);
            }
            $services[$type] = $order_services;
        }
        $order->set_services($services->to_array());
        return $value;
    }
    /**
     * Returns the service items for the given order services
     *
     * @param \Aimeos\Map $services List of items implementing \Aimeos\MShop\Order\Item\Service\Iface with IDs as keys
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Service\Item\Iface with IDs as keys
     */
    protected function get_service_items(\Aimeos\Map $services): \Aimeos\Map
    {
        $list = map();
        foreach ($services as $items) {
            $list->concat(map($items)->get_service_id());
        }
        if ($list->is_empty()) {
            return $list;
        }
        $service_manager = \Aimeos\M_Shop::create($this->context(), 'service');
        $search = $service_manager->filter(true)->add(['service.id' => $list]);
        return $service_manager->search($search, ['media', 'price', 'text']);
    }
}