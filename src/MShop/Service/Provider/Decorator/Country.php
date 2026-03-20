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

use Aimeos\M_Shop\Order\Item\Address;
/**
 * Country-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Country extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['country.billing-include' => ['code' => 'country.billing-include', 'internalcode' => 'country.billing-include', 'label' => 'List of countries allowed for the billing address', 'default' => '', 'required' => false], 'country.billing-exclude' => ['code' => 'country.billing-exclude', 'internalcode' => 'country.billing-exclude', 'label' => 'List of countries not allowed for the billing address', 'default' => '', 'required' => false], 'country.delivery-include' => ['code' => 'country.delivery-include', 'internalcode' => 'country.delivery-include', 'label' => 'List of countries allowed for the delivery address', 'default' => '', 'required' => false], 'country.delivery-exclude' => ['code' => 'country.delivery-exclude', 'internalcode' => 'country.delivery-exclude', 'label' => 'List of countries not allowed for the delivery address', 'default' => '', 'required' => false]];
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
     * Checks if the country code is allowed for the service provider.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        if (($addresses = $basket->get_address(Address\Base::TYPE_DELIVERY)) !== []) {
            foreach ($addresses as $address) {
                $code = strtoupper($address->get_country_id());
                if ($this->check_country_code($code, 'country.delivery-include') === false || $this->check_country_code($code, 'country.delivery-exclude') === true) {
                    return false;
                }
            }
        } elseif (($addresses = $basket->get_address(Address\Base::TYPE_PAYMENT)) !== []) {
            // use billing address if no delivery address is available
            foreach ($addresses as $address) {
                $code = strtoupper($address->get_country_id());
                if ($this->check_country_code($code, 'country.delivery-include') === false || $this->check_country_code($code, 'country.delivery-exclude') === true) {
                    return false;
                }
                if ($this->check_country_code($code, 'country.billing-include') === false || $this->check_country_code($code, 'country.billing-exclude') === true) {
                    return false;
                }
            }
        }
        return $this->get_provider()->is_available($basket);
    }
    /**
     * Checks if the country code is in the list of codes specified by the given key
     *
     * @param string $code Two letter ISO country code in upper case
     * @param string $key Configuration key referring to the country code configuration
     * @return bool|null True if country code is in the list, false if not, null if no codes are availble
     */
    protected function check_country_code(string $code, string $key): ?bool
    {
        if (($str = $this->get_config_value($key)) == null) {
            return null;
        }
        return in_array($code, explode(',', str_replace(' ', '', strtoupper($str))));
    }
}