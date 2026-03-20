<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * 'Time decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Time extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['time.start' => ['code' => 'time.start', 'internalcode' => 'time.start', 'label' => 'Earliest delivery time in 24h "HH:MM" format', 'type' => 'time', 'default' => '00:00', 'required' => false], 'time.end' => ['code' => 'time.end', 'internalcode' => 'time.end', 'label' => 'Latest delivery time in 24h "HH:MM" format', 'type' => 'time', 'default' => '23:59', 'required' => false], 'time.weekdays' => ['code' => 'time.weekdays', 'internalcode' => 'time.weekdays', 'label' => 'Comma separated week days the start and end time is valid for, i.e. number from 1 (Monday) to 7 (Sunday)', 'default' => '1,2,3,4,5,6,7', 'required' => false]];
    private array $fe_config = ['time.hourminute' => ['code' => 'time.hourminute', 'internalcode' => 'hourminute', 'label' => 'Delivery time', 'type' => 'time', 'internaltype' => 'time', 'default' => '', 'required' => true]];
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
        $minute = date('i');
        $feconfig = $this->fe_config;
        $feconfig['time.hourminute']['default'] = date('H:i', time() + ($minute + 15 - $minute % 15) * 60);
        try {
            $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY;
            $service = $this->get_basket_service($basket, $type, $this->get_service_item()->get_code());
            if (($value = $service->get_attribute('time.hourminute', 'delivery')) != '') {
                $feconfig['time.hourminute']['default'] = $value;
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
        if ($result['time.hourminute'] !== null) {
            return $result;
        }
        $time = \DateTime::create_from_format('H:i', $attributes['time.hourminute']);
        $days = explode(',', $this->get_config_value('time.weekdays', '1,2,3,4,5,6,7'));
        if (in_array(date('N'), $days, true)) {
            $start = $this->get_config_value('time.start', '00:00');
            $end = $this->get_config_value('time.end', '23:59');
            if ($time->get_time_stamp() < \DateTime::create_from_format('H:i', $start)->get_time_stamp()) {
                $result['time.hourminute'] = sprintf('Time value before "%1$s"', $start);
            }
            if ($time->get_time_stamp() > \DateTime::create_from_format('H:i', $end)->get_time_stamp()) {
                $result['time.hourminute'] = sprintf('Time value after "%1$s"', $end);
            }
        }
        return $result;
    }
}