<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Free shipping implementation if ordered product sum is above a certain value
 *
 * Sets the shipping costs to zero if the configured threshold is met or exceeded.
 * Only the costs of the delivery option are set to 0.00, not the shipping costs
 * of specific product items!
 *
 * Example:
 * - threshold: 'EUR' => '50.00'
 *
 * There would be no shipping costs for orders of 50 EUR or above. The rebates
 * granted by coupons for example are included into the calculation of the total
 * basket value.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Shipping extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['threshold' => ['code' => 'threshold', 'internalcode' => 'threshold', 'label' => 'Free shipping threshold per currency', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false]];
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
        $plugin = $this->object();
        $p->attach($plugin, 'addCoupon.after');
        $p->attach($plugin, 'deleteCoupon.after');
        $p->attach($plugin, 'setCoupons.after');
        $p->attach($plugin, 'setCoupon.after');
        $p->attach($plugin, 'addProduct.after');
        $p->attach($plugin, 'deleteProduct.after');
        $p->attach($plugin, 'setProducts.after');
        $p->attach($plugin, 'addService.after');
        $p->attach($plugin, 'deleteService.after');
        $p->attach($plugin, 'setServices.after');
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
        $currency = $order->get_price()->get_currency_id();
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY;
        $threshold = $this->get_item_base()->get_config_value('threshold/' . $currency);
        if ($threshold && $service_items = $services->get($type)) {
            foreach ($service_items as $key => $service) {
                $price = $service->get_price();
                if ($this->check_threshold($order->get_products(), $threshold)) {
                    $price = $price->set_rebate($price->get_costs())->set_costs('0.00');
                }
                $service_items[$key] = $service->set_price($price);
            }
            $order->set_services($services->set($type, $service_items)->to_array());
        }
        return $value;
    }
    /**
     * Tests if the shipping threshold is reached and updates the price accordingly
     *
     * @param \Aimeos\Map $orderProducts List of ordered products implementing \Aimeos\MShop\Order\Item\Product\Iface
     * @param string $threshold Threshold for the actual currency
     * @return bool True if threshold is reached, false if not
     */
    protected function check_threshold(\Aimeos\Map $order_products, string $threshold): bool
    {
        $sum = \Aimeos\M_Shop::create($this->context(), 'price')->create();
        foreach ($order_products as $product) {
            if (($product->get_flags() & \Aimeos\M_Shop\Order\Item\Product\Base::FLAG_IMMUTABLE) === 0) {
                $sum = $sum->add_item($product->get_price(), $product->get_quantity());
            }
        }
        if ($sum->get_value() + $sum->get_rebate() >= $threshold) {
            return true;
        }
        return false;
    }
}