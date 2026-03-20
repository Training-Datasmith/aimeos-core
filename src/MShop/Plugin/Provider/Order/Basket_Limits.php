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
 * Checks if ordered product sum and count of products is above a certain value
 *
 * For the basket and checkout summery view, this plugin checks if the products
 * in the basket are still within the configured limits.
 *
 * Available checks are:
 * - min-value: 'EUR' => '10.00' (Minimum total basket value incl. rebates)
 * - max-value: 'EUR' => '10.00' (Maximum total basket value incl. rebates)
 * - min-products: 10 (Minumum number of articles in the basket i.e. basket product * quantity)
 * - max-products: 100 (Maximum number of articles in the basket i.e. basket product * quantity)
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Basket_Limits extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['min-value' => ['code' => 'min-value', 'internalcode' => 'min-value', 'label' => 'Minimum basket value', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false], 'max-value' => ['code' => 'max-value', 'internalcode' => 'max-value', 'label' => 'Maximum basket value', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false], 'min-products' => ['code' => 'min-products', 'internalcode' => 'min-products', 'label' => 'Minimum total products', 'type' => 'int', 'default' => '1', 'required' => false], 'max-products' => ['code' => 'max-products', 'internalcode' => 'max-products', 'label' => 'Maximum total products', 'type' => 'int', 'default' => '', 'required' => false]];
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
        $context = $this->context();
        /** mshop/plugin/provider/order/complete/disable
         * Disables the basket limits check
         *
         * If the BasketLimits plug-in is enabled, it enforces the configured
         * limits before customers or anyone on behalf of them can continue the
         * checkout process.
         *
         * This option enables e.g. call center agents to place orders which
         * doesn't satisfy all requirements. It may be useful if you want to
         * allow them to send free or replacements for lost or damaged products.
         *
         * @param bool True to disable the check, false to keep it enabled
         * @since 2014.03
         */
        if ($context->config()->get('mshop/plugin/provider/order/complete/disable', false) != true) {
            $count = 0;
            $sum = \Aimeos\M_Shop::create($context, 'price')->create();
            foreach ($order->get_products() as $product) {
                $sum->add_item($product->get_price(), $product->get_quantity());
                $count += $product->get_quantity();
            }
            $this->check_limits($sum, $count);
        }
        return $value;
    }
    /**
     * Checks for the configured basket limits.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $sum Total sum of all product price items
     * @param int $count Total number of products in the basket
     * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
     */
    protected function check_limits(\Aimeos\M_Shop\Price\Item\Iface $sum, int $count)
    {
        $config = $this->get_item_base()->get_config();
        $this->check_limits_value($config, $sum);
        $this->check_limits_products($config, $count);
    }
    /**
     * Checks for the configured basket limits.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $sum Total sum of all product price items
     * @param array $config Associative list of configuration key/value pairs
     * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
     */
    protected function check_limits_value(array $config, \Aimeos\M_Shop\Price\Item\Iface $sum)
    {
        $currency_id = $sum->get_currency_id();
        if (isset($config['min-value'][$currency_id]) && is_numeric($config['min-value'][$currency_id]) && $sum->get_value() + $sum->get_rebate() < $config['min-value'][$currency_id]) {
            $msg = $this->context()->translate('mshop', 'The minimum basket value of %1$s isn\'t reached');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, $config['min-value'][$currency_id]));
        }
        if (isset($config['max-value'][$currency_id]) && is_numeric($config['max-value'][$currency_id]) && $sum->get_value() + $sum->get_rebate() > $config['max-value'][$currency_id]) {
            $msg = $this->context()->translate('mshop', 'The maximum basket value of %1$s is exceeded');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, $config['max-value'][$currency_id]));
        }
    }
    /**
     * Checks for the configured basket limits.
     *
     * @param array $config Associative list of configuration key/value pairs
     * @param int $count Total number of products in the basket
     * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
     */
    protected function check_limits_products(array $config, $count)
    {
        if (isset($config['min-products']) && is_numeric($config['min-products']) && $count < $config['min-products']) {
            $msg = $this->context()->translate('mshop', 'The minimum product quantity of %1$d isn\'t reached');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, $config['min-products']));
        }
        if (isset($config['max-products']) && is_numeric($config['max-products']) && $count > $config['max-products']) {
            $msg = $this->context()->translate('mshop', 'The maximum product quantity of %1$d is exceeded');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception(sprintf($msg, $config['max-products']));
        }
    }
}