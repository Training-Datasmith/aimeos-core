<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Sets the tax rate of products and services depending on the country
 *
 * Shops selling into several countries with different tax rates can use this
 * plugin to set a different tax rate in all price items for that countries.
 *
 * The following option is available:
 * - country-taxrates: JSON object of ISO country code as key and tax rate as value
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Taxrates extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['country-taxrates' => ['code' => 'country-taxrates', 'internalcode' => 'country-taxrates', 'label' => 'Tax rate for each two letter ISO country code', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false], 'state-taxrates' => ['code' => 'state-taxrates', 'internalcode' => 'state-taxrates', 'label' => 'Tax rate for each two letter state code', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false], 'services' => ['code' => 'services', 'internalcode' => 'services', 'label' => 'Apply to services as well', 'type' => 'bool', 'internaltype' => 'bool', 'default' => true, 'required' => false]];
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
        $p->attach($plugin, 'addAddress.after');
        $p->attach($plugin, 'setAddresses.after');
        $p->attach($plugin, 'addProduct.after');
        $p->attach($plugin, 'addService.after');
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
     * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        $addrpay = $order->get_address('payment');
        $addrship = $order->get_address('delivery');
        $taxrates = $this->get_config_value('country-taxrates', []);
        $staterates = $this->get_config_value('state-taxrates', []);
        if (($address = reset($addrship)) === false && ($address = reset($addrpay)) === false || !isset($taxrates[$address->get_country_id()]) && !isset($staterates[$address->get_state()])) {
            return $value;
        }
        $taxrate = $staterates[$address->get_state()] ?? $taxrates[$address->get_country_id()];
        if ($value instanceof \Aimeos\M_Shop\Order\Item\Product\Iface) {
            $value->get_price()->set_taxrate($taxrate);
            return $value;
        }
        foreach ($order->get_products() as $order_product) {
            foreach ($order_product->get_products() as $sub_product) {
                $sub_product->get_price()->set_taxrate($taxrate);
            }
            $order_product->get_price()->set_taxrate($taxrate);
        }
        if ($this->get_config_value('services', true)) {
            foreach ($order->get_services() as $order_service_group) {
                foreach ($order_service_group as $order_service) {
                    $order_service->get_price()->set_taxrate($taxrate);
                }
            }
        }
        return $value;
    }
}