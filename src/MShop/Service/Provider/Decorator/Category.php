<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Category-limiting decorator for service providers
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Category extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['category.include' => ['code' => 'category.include', 'internalcode' => 'category.include', 'label' => 'Code of allowed category and sub-categories for the service item', 'default' => '', 'required' => false], 'category.exclude' => ['code' => 'category.exclude', 'internalcode' => 'category.exclude', 'label' => 'Code of category and sub-categories not allowed for the service item', 'default' => '', 'required' => false]];
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
     * Checks if ordered products are in the configured categories to display the service provider.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        $prod_ids = $this->get_product_ids($basket);
        if ($this->check_categories($prod_ids, 'category.include') === false || $this->check_categories($prod_ids, 'category.exclude') === true) {
            return false;
        }
        return $this->get_provider()->is_available($basket);
    }
    /**
     * Checks if at least one of the product is in the configured categories
     *
     * @param array $prodIds List of product IDs
     * @param string $key Configuration key (category.include or category.exclude)
     * @return bool|null True if one catalog code is part of the config, false if not, null for no configuration
     */
    protected function check_categories(array $prod_ids, string $key): ?bool
    {
        if (($codes = $this->get_config_value($key)) == null) {
            return null;
        }
        $config_catalog_ids = $this->get_catalog_ids(explode(',', $codes));
        if (empty($tree_catalog_ids = $this->get_tree_catalog_ids($config_catalog_ids))) {
            return false;
        }
        $types = ['default', 'promotion'];
        $manager = \Aimeos\M_Shop::create($this->context(), 'product');
        // Fetch hidden product too (null for filter)
        $filter = $manager->filter(null)->slice(0, 1);
        $filter->add('product.id', '==', $prod_ids)->add($filter->make('product:has', ['catalog', $types, $tree_catalog_ids]), '!=', null);
        return !$manager->search($filter)->is_empty();
    }
    /**
     * Returns the catalog IDs for the given catalog codes
     *
     * @param array $codes List of catalog codes
     * @return array List of catalog IDs
     */
    protected function get_catalog_ids(array $codes): array
    {
        // Fetch hidden categories too (null for filter)
        $manager = \Aimeos\M_Shop::create($this->context(), 'catalog');
        $filter = $manager->filter(null)->add(['catalog.code' => $codes])->slice(0, count($codes));
        return $manager->search($filter)->keys()->all();
    }
    /**
     * Returns the catalog IDs from the given catalog item and its children
     *
     * @param \Aimeos\MShop\Catalog\Item\Iface $catalogItem Catalog node object
     * @return array List of catalog IDs
     */
    protected function get_node_catalog_ids(\Aimeos\M_Shop\Catalog\Item\Iface $catalog_item): array
    {
        $catalog_ids = [$catalog_item->get_id()];
        foreach ($catalog_item->get_children() as $child_node) {
            if ($child_node->get_status() > 0) {
                $catalog_ids = array_merge($catalog_ids, $this->get_node_catalog_ids($child_node));
            }
        }
        return $catalog_ids;
    }
    /**
     * Returns the products IDs from the products in the basket
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object with ordered products included
     * @return array List of proudct IDs
     */
    protected function get_product_ids(\Aimeos\M_Shop\Order\Item\Iface $basket): array
    {
        $product_ids = [];
        foreach ($basket->get_products() as $product) {
            $product_ids[] = $product->get_product_id();
            if ($parentid = $product->get_parent_product_id()) {
                $product_ids[] = $parentid;
            }
        }
        return array_unique($product_ids);
    }
    /**
     * Returns the catalog codes for the given catalog IDs
     *
     * @param array $catalogIds List of catalog IDs
     * @return array List of catalog codes
     */
    protected function get_tree_catalog_ids(array $catalog_ids): array
    {
        $ids = [];
        $catalog_manager = \Aimeos\M_Shop::create($this->context(), 'catalog');
        foreach ($catalog_ids as $cat_id) {
            $tree_node = $catalog_manager->get_tree($cat_id);
            $ids = array_merge($ids, $this->get_node_catalog_ids($tree_node));
        }
        return array_unique($ids);
    }
}