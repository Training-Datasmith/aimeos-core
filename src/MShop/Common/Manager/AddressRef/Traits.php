<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager\Address_Ref;

/**
 * Common trait for managers retrieving/storing address items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
    /**
     * Returns the outmost decorator of the decorator stack
     *
     * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
     */
    abstract protected function object(): \Aimeos\M_Shop\Common\Manager\Iface;
    /**
     * Creates a new address item object
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Common\Item\Address\Iface New address item object
     */
    public function create_address_item(array $values = []): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->object()->get_sub_manager('address')->create($values);
    }
    /**
     * Returns the address items for the given parent IDs
     *
     * @param string[] $parentIds List of parent IDs
     * @param string $domain Domain of the calling manager
     * @return array Associative list of parent IDs / address IDs as keys and items implementing
     * 	\Aimeos\MShop\Common\Item\Address\Iface as values
     */
    protected function get_address_items(array $parent_ids, string $domain): array
    {
        if (empty($parent_ids)) {
            return [];
        }
        $manager = $this->object()->get_sub_manager('address');
        $search = $manager->filter()->slice(0, 0x7fffffff)->add($domain . '.address.parentid', '==', $parent_ids)->order($domain . '.address.position');
        return $manager->search($search)->group_by($domain . '.address.parentid')->all();
    }
    /**
     * Adds new, updates existing and deletes removed address items
     *
     * @param \Aimeos\MShop\Common\Item\AddressRef\Iface $item Item with referenced items
     * @param string $domain Domain of the calling manager
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Common\Item\AddressRef\Iface Item with saved referenced items
     */
    protected function save_address_items(\Aimeos\M_Shop\Common\Item\Address_Ref\Iface $item, string $domain, bool $fetch = true): \Aimeos\M_Shop\Common\Item\Address_Ref\Iface
    {
        $manager = $this->object()->get_sub_manager('address');
        $manager->delete($item->get_address_items_deleted()->keys());
        foreach ($item->get_address_items() as $idx => $addr_item) {
            if ($addr_item->get_parent_id() != $item->get_id()) {
                $addr_item = $addr_item->set_id(null);
                //create new address item if copied
            }
            $addr_item = $manager->save($addr_item->set_parent_id($item->get_id()), $fetch);
            $item->add_address_item($addr_item, $idx);
        }
        return $item;
    }
}