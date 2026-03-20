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
 * Provides a decorator for managing list items
 *
 * @package MShop
 * @subpackage Common
 */
class Lists extends \Aimeos\M_Shop\Common\Manager\Decorator\Base implements \Aimeos\M_Shop\Common\Manager\Lists_Ref\Iface
{
    use \Aimeos\M_Shop\Common\Manager\Lists_Ref\Traits;
    private string $domain;
    /**
     * Removes multiple items.
     *
     * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $items List of item objects or IDs of the items
     * @return \Aimeos\MShop\Attribute\Manager\Iface Manager object for chaining method calls
     */
    public function delete($items): \Aimeos\M_Shop\Common\Manager\Iface
    {
        $this->get_manager()->delete($items);
        return $this->delete_ref_items($items);
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
        $context = $this->context();
        $keys = array_flip($excludes);
        $excludes[] = 'lists';
        $items = $this->get_manager()->from($entries, $refs, $excludes);
        foreach ($entries as $key => $entry) {
            if (isset($entry['lists']) && $item = $items->get($key)) {
                foreach ($entry['lists'] as $domain => $list) {
                    foreach ($list as $data) {
                        $data = array_diff_key($data, $keys);
                        $list_item = $this->create_list_item()->from_array($data, true);
                        $manager = \Aimeos\M_Shop::create($context, $domain);
                        if ($ref_item = $manager->from([$data])->first()) {
                            $item->add_list_item($domain, $list_item, $ref_item);
                        }
                    }
                }
            }
        }
        return $items;
    }
    /**
     * Returns the attributes that can be used for searching.
     *
     * @param bool $withsub Return also attributes of sub-managers if true
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
     */
    public function get_search_attributes(bool $withsub = true): array
    {
        $domain = $this->domain();
        $alias = $this->alias($domain . '.lists.id');
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL;
        $level = $this->context()->config()->get('mshop/' . $domain . '/manager/sitemode', $level);
        return $this->get_manager()->get_search_attributes($withsub) + $this->create_attributes([$domain . ':has' => ['code' => $domain . ':has()', 'internalcode' => ':site AND :key AND ' . $alias . '."id"', 'internaldeps' => ['LEFT JOIN "mshop_' . $domain . '_list" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'], 'label' => 'Has list item, parameter(<domain>[,<list type>[,<reference ID>]])', 'type' => 'null', 'public' => false, 'function' => function (&$source, array $params) use ($alias, $level): array {
            $keys = [];
            foreach ((array) ($params[1] ?? '') as $type) {
                foreach ((array) ($params[2] ?? '') as $id) {
                    $keys[] = substr($params[0] . '|' . ($type ? $type . '|' : '') . $id, 0, 255);
                }
            }
            $sitestr = $this->site_string($alias . '."siteid"', $level);
            $keystr = $this->to_expression($alias . '."key"', $keys, $params[2] ?? null ? '==' : '=~');
            $source = str_replace([':site', ':key'], [$sitestr, $keystr], $source);
            return $params;
        }], $domain . ':starts' => ['code' => $domain . ':starts()', 'internalcode' => ':site AND :expr AND ' . $alias . '."id"', 'internaldeps' => ['LEFT JOIN "mshop_' . $domain . '_list" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'], 'label' => 'Has list item with start date, parameter(<domain>,<list type>,<after>[,<before>])', 'type' => 'null', 'public' => false, 'function' => function (&$source, array $params) use ($alias, $level): array {
            $expr = [$this->to_expression($alias . '."domain"', $params[0] ?? ''), $this->to_expression($alias . '."type"', $params[1] ?? ''), $this->to_expression($alias . '."start"', $params[2] ?? '', '>=')];
            if (isset($params[3])) {
                $expr[] = $this->to_expression($alias . '."start"', $params[3], '<=');
            }
            $sitestr = $this->site_string($alias . '."siteid"', $level);
            $source = str_replace([':site', ':expr'], [$sitestr, join(' AND ', $expr)], $source);
            return $params;
        }], $domain . ':ends' => ['code' => $domain . ':ends()', 'internalcode' => ':site AND :expr AND ' . $alias . '."id"', 'internaldeps' => ['LEFT JOIN "mshop_' . $domain . '_list" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'], 'label' => 'Has list item with end date, parameter(<domain>,<list type>,<after>[,<before>])', 'type' => 'null', 'public' => false, 'function' => function (&$source, array $params) use ($alias, $level): array {
            $expr = [$this->to_expression($alias . '."domain"', $params[0] ?? ''), $this->to_expression($alias . '."type"', $params[1] ?? ''), $this->to_expression($alias . '."end"', $params[2] ?? '', '>=')];
            if (isset($params[3])) {
                $expr[] = $this->to_expression($alias . '."end"', $params[3], '<=');
            }
            $sitestr = $this->site_string($alias . '."siteid"', $level);
            $source = str_replace([':site', ':expr'], [$sitestr, join(' AND ', $expr)], $source);
            return $params;
        }]]);
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
        $this->save_list_items($item, $this->domain(), $fetch);
        return $this->get_manager()->save_refs($item);
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
        $entries = $this->get_manager()->search_refs($entries, $ref);
        foreach ($this->get_list_items(array_keys($entries), $ref, $this->domain()) as $id => $list_item) {
            $entries[$list_item->get_parent_id()]['.listitems'][$id] = $list_item;
        }
        return $entries;
    }
    /**
     * Returns the domain of the manager
     *
     * @return string Domain of the manager
     */
    protected function domain(): string
    {
        if (!isset($this->domain)) {
            $this->domain = current($this->get_manager()->type()) ?: '';
        }
        return $this->domain;
    }
}