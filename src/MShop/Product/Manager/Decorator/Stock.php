<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024-2026
 * @package MShop
 * @subpackage Product
 */
namespace Aimeos\M_Shop\Product\Manager\Decorator;

/**
 * Provides a decorator for managing stock items
 *
 * @package MShop
 * @subpackage Product
 */
class Stock extends \Aimeos\M_Shop\Common\Manager\Decorator\Base implements \Aimeos\M_Shop\Common\Manager\Iface
{
    /**
     * Creates a new stock item object
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Stock\Item\Iface New stock item object
     */
    public function create_stock_item(array $values = []): \Aimeos\M_Shop\Stock\Item\Iface
    {
        return \Aimeos\M_Shop::create($this->context(), 'stock')->create($values);
    }
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
        $excludes[] = 'stock';
        $items = $this->get_manager()->from($entries, $refs, $excludes);
        foreach ($entries as $key => $entry) {
            if (isset($entry['stock']) && $item = $items->get($key)) {
                foreach ($entry['stock'] as $list) {
                    $list = array_diff_key($list, $keys);
                    $item->add_stock_item($this->create_stock_item()->from_array($list, true));
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
        $this->save_stock_items($item, $fetch);
        return $this->get_manager()->save_refs($item);
    }
    /**
     * Merges the data from the given map and the referenced items
     *
     * @param array $entries Associative list of ID as key and the associative list of stock key/value pairs as values
     * @param array $ref List of referenced items to fetch and add to the entries
     * @return array Associative list of ID as key and the updated entries as value
     */
    public function search_refs(array $entries, array $ref): array
    {
        $entries = $this->get_manager()->search_refs($entries, $ref);
        if ($this->has_ref($ref, 'stock')) {
            foreach ($this->get_stock_items(array_keys($entries), $ref) as $id => $list) {
                $entries[$id]['.stock'] = $list;
            }
        }
        return $entries;
    }
    /**
     * Returns the stock items for the given parent IDs
     *
     * @param string[] $prodIds List of parent IDs
     * @param array $ref Referenced items that should be fetched too
     * @return array Associative list of parent IDs / stock IDs as keys and items implementing
     * 	\Aimeos\MShop\Stock\Item\Iface as values
     */
    protected function get_stock_items(array $prod_ids, array $ref = []): array
    {
        if (empty($prod_ids)) {
            return [];
        }
        $manager = \Aimeos\M_Shop::create($this->context(), 'stock');
        $filter = $manager->filter()->slice(0, 0x7fffffff)->add('stock.productid', '==', $prod_ids);
        $types = isset($ref['stock']) && is_array($ref['stock']) ? $ref['stock'] : null;
        if (!empty($types)) {
            $filter->add('stock.type', '==', $types);
        }
        return $manager->search($filter, $ref ?? [])->group_by('stock.productid')->all();
    }
    /**
     * Adds new, updates existing and deletes removed stock items
     *
     * @param \Aimeos\MShop\Product\Item\Iface $item Item with stock items
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Product\Item\Iface Item with saved stock items
     */
    protected function save_stock_items(\Aimeos\M_Shop\Product\Item\Iface $item, bool $fetch = true): \Aimeos\M_Shop\Product\Item\Iface
    {
        $stock_manager = \Aimeos\M_Shop::create($this->context(), 'stock');
        $stock_manager->delete($item->get_stock_items_deleted());
        $stock_items = $item->get_stock_items();
        foreach ($stock_items as $stock_item) {
            if ($stock_item->get_product_id() != $item->get_id()) {
                $stock_item->set_id(null);
                // create new stock item if copied
            }
            $stock_item->set_product_id($item->get_id());
        }
        $stock_manager->save($stock_items, $fetch);
        return $item;
    }
}