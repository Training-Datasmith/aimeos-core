<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Decorator for reduction of basket value.
 *
 * @package MShop
 * @subpackage Service
 */
class Reduction extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['reduction.percent' => ['code' => 'reduction.percent', 'internalcode' => 'reduction.percent', 'label' => 'Decimal value in percent (positive or negative)', 'type' => 'number', 'default' => '', 'required' => true], 'reduction.include-costs' => ['code' => 'reduction.include-costs', 'internalcode' => 'reduction.include-costs', 'label' => 'Include delivery/payments costs in reduction calculation', 'type' => 'bool', 'default' => '0', 'required' => false]];
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
     * Returns the price when using the provider.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @param array $options Selected options by customer from frontend
     * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
     */
    public function calc_price(\Aimeos\M_Shop\Order\Item\Iface $basket, array $options = []): \Aimeos\M_Shop\Price\Item\Iface
    {
        $price = $this->get_provider()->calc_price($basket, $options);
        if ($this->get_config_value('reduction.include-costs')) {
            $sub = $price->get_costs() * $this->get_config_value('reduction.percent') / 100;
            $price->set_costs($price->get_costs() - $sub)->set_rebate($price->get_rebate() + $sub);
        }
        $sub = $basket->get_price()->get_value() * $this->get_config_value('reduction.percent') / 100;
        return $price->set_value($price->get_value() - $sub)->set_rebate($price->get_rebate() + $sub);
    }
}