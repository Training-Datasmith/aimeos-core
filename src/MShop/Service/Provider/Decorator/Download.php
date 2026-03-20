<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Download check decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Download extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['download.all' => ['code' => 'download.all', 'internalcode' => 'download.all', 'label' => 'Check products: "1" = all must be downloads, "0" = at least one is no download', 'type' => 'bool', 'default' => '', 'required' => true]];
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
     * Checks if the service provider should be available.
     *
     * Tests products in basket if they have a download attribute. The method
     * returns true if "download.all" is "1" and all products contain the
     * attribute resp. if "download.all" is "0" and at least one product
     * contains no download attribute.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        if ((bool) $this->get_config_value('download.all') === true) {
            foreach ($basket->get_products() as $product) {
                if ($product->get_attribute('download', 'hidden') === null) {
                    return false;
                }
            }
            return $this->get_provider()->is_available($basket);
        }
        foreach ($basket->get_products() as $product) {
            if ($product->get_attribute('download', 'hidden') === null) {
                return $this->get_provider()->is_available($basket);
            }
        }
        return false;
    }
}