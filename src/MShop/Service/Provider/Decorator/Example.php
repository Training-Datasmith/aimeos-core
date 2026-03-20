<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Example decorator for service provider.
 *
 * @package MShop
 * @subpackage Service
 */
class Example extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['country' => ['code' => 'country', 'internalcode' => 'country', 'label' => 'Country', 'default' => '', 'required' => true]];
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
        $list = $this->get_provider()->get_config_be();
        foreach ($this->be_config as $key => $config) {
            $list[$key] = new \Aimeos\Base\Criteria\Attribute\Standard($config);
        }
        return $list;
    }
    /**
     * Checks if payment provider can be used based on the basket content.
     * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        if ($basket->locale()->get_language_id() === 'en') {
            return $this->get_provider()->is_available($basket);
        }
        return false;
    }
}