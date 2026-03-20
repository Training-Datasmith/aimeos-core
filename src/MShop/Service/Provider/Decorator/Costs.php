<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Decorator for service providers adding additional costs.
 *
 * @package MShop
 * @subpackage Service
 */
class Costs extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['costs.percent' => ['code' => 'costs.percent', 'internalcode' => 'costs.percent', 'label' => 'Costs: Decimal percent value', 'type' => 'number', 'default' => 0, 'required' => true]];
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
     * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
     * the basket content, e.g. 2% of the value as transaction cost.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @param array $options Selected options by customer from frontend
     * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
     */
    public function calc_price(\Aimeos\M_Shop\Order\Item\Iface $basket, array $options = []): \Aimeos\M_Shop\Price\Item\Iface
    {
        $config = $this->get_service_item()->get_config();
        if (!isset($config['costs.percent'])) {
            $msg = $this->context()->translate('mshop', 'Missing configuration "%1$s"');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, 'costs.percent'));
        }
        $value = $basket->get_price()->get_value() * $config['costs.percent'] / 100;
        $price = $this->get_provider()->calc_price($basket, $options);
        $price->set_costs($price->get_costs() + $value);
        return $price;
    }
}