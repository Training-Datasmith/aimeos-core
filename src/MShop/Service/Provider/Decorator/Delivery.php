<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Delivery type decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Delivery extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['delivery.partial' => ['code' => 'delivery.partial', 'internalcode' => 'delivery.partial', 'label' => 'Choice of partitial delivery', 'type' => 'bool', 'default' => '0', 'required' => false], 'delivery.collective' => ['code' => 'delivery.collective', 'internalcode' => 'delivery.collective', 'label' => 'Choice of collective delivery', 'type' => 'bool', 'default' => '0', 'required' => false]];
    private array $fe_config = ['delivery.type' => ['code' => 'delivery.type', 'internalcode' => 'type', 'label' => 'Delivery type', 'type' => 'list', 'default' => [1 => 'complete delivery'], 'required' => true]];
    /**
     * Initializes a new service provider object using the given context object.
     *
     * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider or decorator
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
     */
    public function __construct(\Aimeos\M_Shop\Service\Provider\Iface $provider, \Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Service\Item\Iface $service_item)
    {
        parent::__construct($provider, $context, $service_item);
        if ($this->get_config_value('delivery.partial', 0)) {
            $this->fe_config['delivery.type']['default'][0] = 'partial delivery';
        }
        if ($this->get_config_value('delivery.collective', 0)) {
            $this->fe_config['delivery.type']['default'][2] = 'collective delivery';
        }
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
        $error = $this->get_provider()->check_config_be($attributes);
        return $error + $this->check_config($this->be_config, $attributes);
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
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the frontend.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_fe(\Aimeos\M_Shop\Order\Item\Iface $basket): array
    {
        $feconfig = $this->fe_config;
        try {
            $values = $this->fe_config['delivery.type']['default'];
            $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY;
            $service = $this->get_basket_service($basket, $type, $this->get_service_item()->get_code());
            if (($value = $service->get_attribute('delivery.type', 'delivery')) != '') {
                $feconfig['delivery.type']['default'] = $this->sort($values, (int) $value);
            } else {
                $feconfig['delivery.type']['default'] = $values;
            }
        } catch (\Aimeos\M_Shop\Service\Exception) {
        }
        // If service isn't available
        return array_merge($this->get_provider()->get_config_fe($basket), $this->get_config_items($feconfig));
    }
    /**
     * Checks the frontend configuration attributes for validity.
     *
     * @param array $attributes Attributes entered by the customer during the checkout process
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
     */
    public function check_config_fe(array $attributes): array
    {
        $result = $this->get_provider()->check_config_fe($attributes);
        $result += array_merge($result, $this->check_config($this->fe_config, $attributes));
        if (isset($attributes['delivery.type']) && !isset($this->fe_config['delivery.type']['default'][$attributes['delivery.type']])) {
            $result['delivery.type'] = $this->context()->translate('mshop', 'Invalid delivery type');
        }
        return $result;
    }
    /**
     * Sorts the entry with the given key to the first position
     *
     * @param array $values Associative list of keys and codes
     * @param int $value Key that should be at first position
     * @return array Sorted associative array
     */
    protected function sort(array $values, int $value): array
    {
        if (!isset($values[$value])) {
            return $values;
        }
        $code = $values[$value];
        unset($values[$value]);
        return [$value => $code] + $values;
    }
}