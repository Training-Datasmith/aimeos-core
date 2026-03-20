<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * BasketValues decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Basket_Values extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['basketvalues.total-value-min' => ['code' => 'basketvalues.total-value-min', 'internalcode' => 'basketvalues.total-value-min', 'label' => 'Minimum total value of the basket', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false], 'basketvalues.total-value-max' => ['code' => 'basketvalues.total-value-max', 'internalcode' => 'basketvalues.total-value-max', 'label' => 'Maximum total value of the basket', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        return $this->check_config($this->be_config, $attributes);
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
     * Checks for the min/max order value.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $base Basic order of the customer
     * @return bool True if the basket matches the constraints, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $base): bool
    {
        $price = $base->get_price();
        $currency = $price->get_currency_id();
        $value = $price->get_value() + $price->get_rebate();
        $minvalue = $this->get_config_value('basketvalues.total-value-min', []);
        if (isset($minvalue[$currency]) && $minvalue[$currency] > $value) {
            return false;
        }
        $maxvalue = $this->get_config_value('basketvalues.total-value-max', []);
        if (isset($maxvalue[$currency]) && $maxvalue[$currency] < $value) {
            return false;
        }
        return parent::is_available($base);
    }
}