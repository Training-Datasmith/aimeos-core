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
 * Provides a decorator for managing property items
 *
 * @package MShop
 * @subpackage Common
 */
class Property extends \Aimeos\M_Shop\Common\Manager\Decorator\Base implements \Aimeos\M_Shop\Common\Manager\Property_Ref\Iface
{
    use \Aimeos\M_Shop\Common\Manager\Property_Ref\Traits;
    private string $domain;
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
        $excludes[] = 'property';
        $items = $this->get_manager()->from($entries, $refs, $excludes);
        foreach ($entries as $key => $entry) {
            if (isset($entry['property']) && $item = $items->get($key)) {
                foreach ($entry['property'] as $list) {
                    $list = array_diff_key($list, $keys);
                    $item->add_property_item($this->create_property_item()->from_array($list, true));
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
        $alias = $this->alias($domain . '.property.id');
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL;
        $level = $this->context()->config()->get('mshop/' . $domain . '/manager/sitemode', $level);
        return $this->get_manager()->get_search_attributes($withsub) + $this->create_attributes([$domain . ':prop' => ['code' => $domain . ':prop()', 'internalcode' => ':site AND :key AND ' . $alias . '."id"', 'internaldeps' => ['LEFT JOIN "mshop_' . $domain . '_property" AS ' . $alias . ' ON ( ' . $alias . '."parentid" = ' . $this->alias() . '."id" )'], 'label' => 'Has property item, parameter(<property type>[,<language code>[,<property value>]])', 'type' => 'null', 'public' => false, 'function' => function (&$source, array $params) use ($alias, $level): array {
            $keys = [];
            $langs = array_key_exists(1, $params) ? $params[1] ?? 'null' : '';
            foreach ((array) $langs as $lang) {
                foreach ((array) ($params[2] ?? '') as $val) {
                    $keys[] = substr($params[0] . '|' . ($lang === null ? 'null|' : ($lang ? $lang . '|' : '')) . $val, 0, 255);
                }
            }
            $sitestr = $this->site_string($alias . '."siteid"', $level);
            $keystr = $this->to_expression($alias . '."key"', $keys, $params[2] ?? null ? '==' : '=~');
            $source = str_replace([':site', ':key'], [$sitestr, $keystr], $source);
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
        $this->save_property_items($item, $this->domain(), $fetch);
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
        $domain = $this->domain();
        if ($this->has_ref($ref, $domain . '/property')) {
            foreach ($this->get_property_items(array_keys($entries), $domain, $ref) as $id => $list) {
                $entries[$id]['.propitems'] = $list;
            }
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