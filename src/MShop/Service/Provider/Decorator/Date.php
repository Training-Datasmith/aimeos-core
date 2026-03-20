<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Date decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Date extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['date.minimumdays' => ['code' => 'date.minimumdays', 'internalcode' => 'date.minimumdays', 'label' => 'Miniumn number of days to wait when selecting dates', 'type' => 'int', 'default' => '0', 'required' => false]];
    private array $fe_config = ['date.value' => ['code' => 'date.value', 'internalcode' => 'value', 'label' => 'Delivery date', 'type' => 'date', 'default' => '', 'required' => true]];
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
            $days = $this->get_config_value('date.minimumdays', 0);
            $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY;
            $service = $this->get_basket_service($basket, $type, $this->get_service_item()->get_code());
            if (($value = $service->get_attribute('date.value', 'delivery')) == '') {
                $feconfig['date.value']['default'] = date('Y-m-d', time() + 86400 * $days);
            } else {
                $feconfig['date.value']['default'] = $value;
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
        $result = array_merge($result, $this->check_config($this->fe_config, $attributes));
        if ($result['date.value'] !== null) {
            return $result;
        }
        $minimum = date('Y-m-d', time() + 86400 * $this->get_config_value('date.minimumdays', 0));
        if ($attributes['date.value'] < $minimum) {
            $result['date.value'] = sprintf('Date value before "%1$s"', $minimum);
        }
        return $result;
    }
}