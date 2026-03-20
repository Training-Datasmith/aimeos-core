<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager\Property_Ref;

/**
 * Common trait for managers retrieving/storing property items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
    /**
     * Returns the context object.
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    abstract protected function context(): \Aimeos\M_Shop\Context_Iface;
    /**
     * Returns the domain of the manager
     *
     * @return string Domain of the manager
     */
    abstract protected function domain(): string;
    /**
     * Returns the outmost decorator of the decorator stack
     *
     * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
     */
    abstract protected function object(): \Aimeos\M_Shop\Common\Manager\Iface;
    /**
     * Creates a new property item object
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
     */
    public function create_property_item(array $values = []): \Aimeos\M_Shop\Common\Item\Property\Iface
    {
        $domain = $this->domain();
        $context = $this->context();
        $values['.languageid'] = $context->locale()->get_language_id();
        $values[$domain . '.property.siteid'] ??= $context->locale()->get_site_id();
        return new \Aimeos\M_Shop\Common\Item\Property\Standard($domain . '.property.', $values);
    }
    /**
     * Returns the property items for the given parent IDs
     *
     * @param string[] $parentIds List of parent IDs
     * @param string $domain Domain of the calling manager
     * @param array|null $ref Referenced items that should be fetched too
     * @return array Associative list of parent IDs / property IDs as keys and items implementing
     * 	\Aimeos\MShop\Common\Item\Property\Iface as values
     */
    protected function get_property_items(array $parent_ids, string $domain, ?array $ref = []): array
    {
        if (empty($parent_ids)) {
            return [];
        }
        $manager = $this->object()->get_sub_manager('property');
        $filter = $manager->filter()->slice(0, 0x7fffffff)->add($domain . '.property.parentid', '==', $parent_ids);
        $name = $domain . '/property';
        $types = $ref && isset($ref[$name]) && is_array($ref[$name]) ? $ref[$name] : null;
        if (!empty($types)) {
            $filter->add($domain . '.property.type', '==', $types);
        }
        return $manager->search($filter, $ref ?? [])->group_by($domain . '.property.parentid')->all();
    }
    /**
     * Adds new, updates existing and deletes removed property items
     *
     * @param \Aimeos\MShop\Common\Item\PropertyRef\Iface $item Item with referenced items
     * @param string $domain Domain of the calling manager
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Item with saved referenced items
     */
    protected function save_property_items(\Aimeos\M_Shop\Common\Item\Property_Ref\Iface $item, string $domain, bool $fetch = true): \Aimeos\M_Shop\Common\Item\Property_Ref\Iface
    {
        $prop_manager = $this->object()->get_sub_manager('property');
        $prop_manager->delete($item->get_property_items_deleted());
        $prop_items = $item->get_property_items(null, false);
        foreach ($prop_items as $prop_item) {
            if ($prop_item->get_parent_id() != $item->get_id()) {
                $prop_item->set_id(null);
                // create new property item if copied
            }
            $prop_item->set_parent_id($item->get_id());
        }
        $prop_manager->save($prop_items, $fetch);
        return $item;
    }
}