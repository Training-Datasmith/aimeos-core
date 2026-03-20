<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Product-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Product extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['product.include' => ['code' => 'product.include', 'internalcode' => 'product.include', 'label' => 'Codes of allowed products for the service item', 'default' => '', 'required' => false], 'product.exclude' => ['code' => 'product.exclude', 'internalcode' => 'product.exclude', 'label' => 'Codes of the products not allowed for the service item', 'default' => '', 'required' => false]];
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
     * Checks if the products are withing the allowed code is allowed for the service provider.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        $codes = $this->get_product_codes($basket);
        if ($this->check_products($codes, 'product.include') === false || $this->check_products($codes, 'product.exclude') === true) {
            return false;
        }
        return $this->get_provider()->is_available($basket);
    }
    /**
     * Checks if at least one of the given categories is configured
     *
     * @param array $catalogIds List of product IDs
     * @param string $key Configuration key (product.include or product.exclude)
     * @return bool|null True if one catalog code is part of the config, false if not, null for no configuration
     */
    protected function check_products(array $prodcodes, string $key): ?bool
    {
        if (($codes = $this->get_config_value([$key])) == null) {
            return null;
        }
        return array_intersect($prodcodes, explode(',', $codes)) !== [];
    }
    /**
     * Returns the products codes for the products in the basket
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object with ordered products included
     * @return array List of product codes
     */
    protected function get_product_codes(\Aimeos\M_Shop\Order\Item\Iface $basket): array
    {
        $codes = [];
        foreach ($basket->get_products() as $product) {
            $codes[] = $product->get_product_code();
            foreach ($product->get_products() as $subproduct) {
                $codes[] = $subproduct->get_product_code();
            }
        }
        return $codes;
    }
}