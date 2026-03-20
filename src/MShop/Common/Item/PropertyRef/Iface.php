<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Property_Ref;

/**
 * Common interface for items containing property items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface
{
    /**
     * Adds a new property item or overwrite an existing one
     *
     * @param \Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function add_property_item(\Aimeos\M_Shop\Common\Item\Property\Iface $item): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface;
    /**
     * Adds new property items or overwrite existing ones
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function add_property_items(iterable $items): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface;
    /**
     * Removes an existing property item
     *
     * @param \Aimeos\MShop\Common\Item\Property\Iface $item Existing property item
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function delete_property_item(\Aimeos\M_Shop\Common\Item\Property\Iface $item): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface;
    /**
     * Removes a list of existing property items
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface[] $items Existing property items
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function delete_property_items(iterable $items): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface;
    /**
     * Returns the deleted property items
     *
     * @return \Aimeos\Map Property items implementing \Aimeos\MShop\Common\Item\Property\Iface
     */
    public function get_property_items_deleted(): \Aimeos\Map;
    /**
     * Returns the property values for the given type
     *
     * @param string $type Type of the properties
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\Map List of property values
     */
    public function get_properties(string $type, bool $active = true): \Aimeos\Map;
    /**
     * Returns the property item for the given type, language and value
     *
     * @param string $type Name of the property type
     * @param string|null $langId ISO language code (e.g. "en" or "en_US") or null if not language specific
     * @param string $value Value of the property
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\MShop\Common\Item\Property\Iface|null Matching property item or null if none
     */
    public function get_property_item(string $type, ?string $lang_id, string $value, bool $active = true): ?\Aimeos\M_Shop\Common\Item\Property\Iface;
    /**
     * Returns the property items of the product
     *
     * @param array|string|null $type Name of the property item type or null for all
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\Map List of property IDs as keys and property items implementing \Aimeos\MShop\Common\Item\Property\Iface
     */
    public function get_property_items($type = null, bool $active = true): \Aimeos\Map;
    /**
     * Adds a new property item or overwrite an existing one
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface[] $items New list of property items
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function set_property_items(iterable $items): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface;
}