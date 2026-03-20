<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager\Decorator;

/**
 * Provides a decorator for fetching site items
 *
 * @package MShop
 * @subpackage Common
 */
class Address extends \Aimeos\M_Shop\Common\Manager\Decorator\Base
{
    use \Aimeos\M_Shop\Common\Manager\Address_Ref\Traits;
    /**
     * Creates objects from the given array
     *
     * @param iterable $entries List of associative arrays with key/value pairs
     * @param array $refs List of domains to retrieve list items and referenced items for
     * @param array $excludes List of keys which shouldn't be used when creating the items
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
     */
    public function from(iterable $entries, array $refs = [], array $excludes = []): \Aimeos\Map
    {
        $keys = array_flip($excludes);
        $excludes[] = 'address';
        $items = $this->get_manager()->from($entries, $refs, $excludes);
        foreach ($entries as $key => $entry) {
            if (isset($entry['address']) && $item = $items->get($key)) {
                foreach ($entry['address'] as $list) {
                    $list = array_diff_key($list, $keys);
                    $item->add_address_item($this->create_address_item()->from_array($list, true));
                }
            }
        }
        return $items;
    }
    /**
     * Saves the dependent items of the item
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item object
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Common\Item\Iface Updated item
     */
    public function save_refs(\Aimeos\M_Shop\Common\Item\Iface $item, bool $fetch = true): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->get_manager()->save_refs($item, $fetch);
        $this->save_address_items($item, $this->domain(), $fetch);
        return $item;
    }
    /**
     * Merges the data from the given map and the referenced items
     *
     * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
     * @param array $ref List of referenced items to fetch and add to the entries
     * @return array Associative list of ID as key and the updated entries as value
     */
    public function search_refs(array $entries, array $ref): array
    {
        $domain = $this->domain();
        $entries = $this->get_manager()->search_refs($entries, $ref);
        if ($this->has_ref($ref, $domain . '/address')) {
            foreach ($this->get_address_items(array_keys($entries), $domain) as $id => $list) {
                $entries[$id]['.addritems'] = $list;
            }
        }
        return $entries;
    }
}