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
 * Product limit implementation if count or sum of a single or of all products in an order exceeds given limit
 *
 * Enforces product restrictions like
 * - single-number-max: 10 (Maximum times a single product can be bought in one order)
 * - total-number-max: 100 (Maximum number of products that can be in the basket, e.g. basket product * quantity)
 * - single-value-max: 'EUR' => '100.00' (Maximum amount for one product, i.e. price * quantity)
 * - total-value-max: 'EUR' => '1000.00' (Maximum amount for all product, i.e. basket product * price * quantity)
 *
 * These limits are enforced if any product in the basket changes.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Product_Limit extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['single-number-max' => ['code' => 'single-number-max', 'internalcode' => 'single-number-max', 'label' => 'Maximum product quantity', 'type' => 'int', 'default' => '', 'required' => false], 'total-number-max' => ['code' => 'total-number-max', 'internalcode' => 'total-number-max', 'label' => 'Maximum total products in basket', 'type' => 'int', 'default' => '', 'required' => false], 'single-value-max' => ['code' => 'single-value-max', 'internalcode' => 'single-value-max', 'label' => 'Maximum product value', 'type' => 'map', 'internaltype' => 'array', 'default' => '{}', 'required' => false], 'total-value-max' => ['code' => 'total-value-max', 'internalcode' => 'total-value-max', 'label' => 'Maximum total basket value', 'type' => 'map', 'internaltype' => 'array', 'default' => '{}', 'required' => false]];
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
     * Subscribes itself to a publisher.
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
     * Receives a notification from a publisher object.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        $list = map($value);
        $list->implements(\Aimeos\M_Shop\Order\Item\Product\Iface::class, true);
        foreach ($list as $entry) {
            $this->check_without_currency($order, $entry);
            $this->check_with_currency($order, $entry);
        }
        return $value;
    }
    /**
     * Checks for the product limits when the configuration doesn't contain limits per currency.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @param \Aimeos\MShop\Order\Item\Product\Iface $value Order product item
     * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
     */
    protected function check_without_currency(\Aimeos\M_Shop\Order\Item\Iface $order, \Aimeos\M_Shop\Order\Item\Product\Iface $value)
    {
        $config = $this->get_item_base()->get_config();
        if (isset($config['single-number-max']) && !is_array($config['single-number-max']) && $value->get_quantity() > (int) $config['single-number-max']) {
            $value->set_quantity($config['single-number-max']);
            // reset to allowed value
            $msg = $this->context()->translate('mshop', 'The maximum product quantity is %1$d');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, (int) $config['single-number-max']));
        }
        if (isset($config['total-number-max']) && !is_array($config['total-number-max'])) {
            $total = $value->get_quantity();
            foreach ($order->get_products() as $product) {
                $total += $product->get_quantity();
            }
            if ($total > (int) $config['total-number-max']) {
                $msg = $this->context()->translate('mshop', 'The maximum quantity of all products is %1$d');
                throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, (int) $config['total-number-max']));
            }
        }
    }
    /**
     * Checks for the product limits when the configuration contains limits per currency.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @param \Aimeos\MShop\Order\Item\Product\Iface $value Order product item
     * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
     */
    protected function check_with_currency(\Aimeos\M_Shop\Order\Item\Iface $order, \Aimeos\M_Shop\Order\Item\Product\Iface $value)
    {
        $config = $this->get_item_base()->get_config();
        $currency_id = $value->get_price()->get_currency_id();
        if (isset($config['single-value-max'][$currency_id]) && $value->get_price()->get_value() * $value->get_quantity() > (float) $config['single-value-max'][$currency_id]) {
            $msg = $this->context()->translate('mshop', 'The maximum product value is %1$s');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, $config['single-value-max'][$currency_id]));
        }
        if (isset($config['total-value-max'][$currency_id])) {
            $price = clone $value->get_price();
            $price->set_value($price->get_value() * $value->get_quantity());
            foreach ($order->get_products() as $product) {
                $price->add_item($product->get_price(), $product->get_quantity());
            }
            if ((float) $price->get_value() > (float) $config['total-value-max'][$currency_id]) {
                $msg = $this->context()->translate('mshop', 'The maximum value of all products is %1$s');
                throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, $config['total-value-max'][$currency_id]));
            }
        }
    }
}