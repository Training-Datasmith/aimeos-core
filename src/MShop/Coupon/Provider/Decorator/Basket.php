<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider\Decorator;

/**
 * Basket decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Basket extends \Aimeos\M_Shop\Coupon\Provider\Decorator\Base implements \Aimeos\M_Shop\Coupon\Provider\Decorator\Iface
{
    private array $be_config = ['basket.total-value-min' => ['code' => 'basket.total-value-min', 'internalcode' => 'basket.total-value-min', 'label' => 'Minimum total value of the basket', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false], 'basket.total-value-max' => ['code' => 'basket.total-value-max', 'internalcode' => 'basket.total-value-max', 'label' => 'Maximum total value of the basket', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => false]];
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
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return bool True if the basket matches the constraints, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $order): bool
    {
        $price = $order->get_price();
        $currency = $price->get_currency_id();
        $value = $price->get_value() + $price->get_rebate();
        $minvalue = $this->get_config_value('basket.total-value-min', []);
        if (isset($minvalue[$currency]) && $minvalue[$currency] >= $value) {
            return false;
        }
        $maxvalue = $this->get_config_value('basket.total-value-max', []);
        if (isset($maxvalue[$currency]) && $maxvalue[$currency] <= $value) {
            return false;
        }
        return parent::is_available($order);
    }
}