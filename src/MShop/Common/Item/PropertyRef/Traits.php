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
 * Common trait for items containing property items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
    private array $prop_items = [];
    private array $prop_rm_items = [];
    private int $prop_max = 0;
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        parent::__clone();
        foreach ($this->prop_items as $key => $item) {
            $this->prop_items[$key] = clone $item;
        }
        foreach ($this->prop_rm_items as $key => $item) {
            $this->prop_rm_items[$key] = clone $item;
        }
    }
    /**
     * Adds a new property item or overwrite an existing one
     *
     * @param \Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function add_property_item(\Aimeos\M_Shop\Common\Item\Property\Iface $item): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface
    {
        $id = $item->get_id() ?: '_' . $this->get_id() . '_' . $item->get_type() . '_' . $item->get_language_id() . '_' . $item->get_value();
        unset($this->prop_items[$id]);
        // append at the end
        $this->prop_items[$id] = $item;
        return $this;
    }
    /**
     * Adds new property items or overwrite existing ones
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function add_property_items(iterable $items): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface
    {
        foreach ($items as $item) {
            $this->add_property_item($item);
        }
        return $this;
    }
    /**
     * Removes an existing property item
     *
     * @param \Aimeos\MShop\Common\Item\Property\Iface $item Existing property item
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function delete_property_item(\Aimeos\M_Shop\Common\Item\Property\Iface $item): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface
    {
        $id = $item->get_id();
        if (isset($this->prop_items[$id])) {
            $this->prop_rm_items[$id] = $item;
            unset($this->prop_items[$id]);
            return $this;
        }
        $id = '_' . $this->get_id() . '_' . $item->get_type() . '_' . $item->get_language_id() . '_' . $item->get_value();
        if (isset($this->prop_items[$id])) {
            $this->prop_rm_items[$id] = $item;
            unset($this->prop_items[$id]);
        }
        return $this;
    }
    /**
     * Removes a list of existing property items
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface[] $items Existing property items
     * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
     * @throws \Aimeos\MShop\Exception If an item isn't a property item or isn't found
     */
    public function delete_property_items(iterable $items): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface
    {
        foreach ($items as $item) {
            $this->delete_property_item($item);
        }
        return $this;
    }
    /**
     * Returns the deleted property items
     *
     * @return \Aimeos\Map Property items implementing \Aimeos\MShop\Common\Item\Property\Iface
     */
    public function get_property_items_deleted(): \Aimeos\Map
    {
        return map($this->prop_rm_items);
    }
    /**
     * Returns the property values for the given type
     *
     * @param string $type Type of the properties
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\Map List of property values
     */
    public function get_properties(string $type, bool $active = true): \Aimeos\Map
    {
        $list = [];
        foreach ($this->get_property_items($type, $active) as $id => $item) {
            $list[$id] = $item->get_value();
        }
        return map($list);
    }
    /**
     * Returns the property item for the given type, language and value
     *
     * @param string $type Name of the property type
     * @param string|null $langId ISO language code (e.g. "en" or "en_US") or null if not language specific
     * @param string $value Value of the property
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\MShop\Common\Item\Property\Iface|null Matching property item or null if none
     */
    public function get_property_item(string $type, ?string $lang_id, string $value, bool $active = true): ?\Aimeos\M_Shop\Common\Item\Property\Iface
    {
        foreach ($this->prop_items as $prop_item) {
            if ($prop_item->get_type() === $type && $prop_item->get_language_id() === $lang_id && $prop_item->get_value() === $value && ($active === false || $prop_item->is_available())) {
                return $prop_item;
            }
        }
        return null;
    }
    /**
     * Returns the property items of the product
     *
     * @param array|string|null $type Name of the property item type or null for all
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\Map List of property IDs as keys and property items implementing \Aimeos\MShop\Common\Item\Property\Iface
     */
    public function get_property_items($type = null, bool $active = true): \Aimeos\Map
    {
        $list = [];
        foreach ($this->prop_items as $prop_id => $prop_item) {
            if (($type === null || in_array($prop_item->get_type(), (array) $type)) && ($active === false || $prop_item->is_available())) {
                $list[$prop_id] = $prop_item;
            }
        }
        return map($list);
    }
    /**
     * Adds a new property item or overwrite an existing one
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface[] $items New list of property items
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
     */
    public function set_property_items(iterable $items): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface
    {
        $list = [];
        foreach ($items as $p) {
            $id = $p->get_id() ?: '_' . $this->get_id() . '_' . $p->get_type() . '_' . $p->get_language_id() . '_' . $p->get_value();
            unset($this->prop_items[$id]);
            $list[$id] = $p;
        }
        $this->delete_property_items($this->prop_items);
        $this->prop_items = $list;
        return $this;
    }
    /**
     * Returns the unique ID of the item.
     *
     * @return string|null ID of the item
     */
    abstract public function get_id(): ?string;
    /**
     * Sets the property items in the trait
     *
     * @param \Aimeos\MShop\Common\Item\Property\Iface[] $items Property items
     */
    protected function init_property_items(array $items)
    {
        $this->prop_items = $items;
    }
}