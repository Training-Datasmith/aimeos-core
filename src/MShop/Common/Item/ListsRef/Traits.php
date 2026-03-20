<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Lists_Ref;

/**
 * Common trait for items containing list items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
    private array $list_items = [];
    private array $list_rm_items = [];
    private array $list_map = [];
    private int $list_max = 0;
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        parent::__clone();
        foreach ($this->list_items as $domain => $list) {
            foreach ($list as $id => $item) {
                $this->list_items[$domain][$id] = clone $item;
            }
        }
        foreach ($this->list_rm_items as $key => $item) {
            $this->list_rm_items[$key] = clone $item;
        }
    }
    /**
     * Returns the unique ID of the item.
     *
     * @return string|null ID of the item
     */
    abstract public function get_id(): ?string;
    /**
     * Returns the item type
     *
     * @return string Item type, subtypes are separated by slashes
     */
    abstract public function get_resource_type(): string;
    /**
     * Registers a custom macro that has access to the class properties if called non-static
     *
     * @param string $name Macro name
     * @param \Closure|null $function Anonymous function
     * @return \Closure|null Registered function
     */
    abstract public static function macro(string $name, ?\Closure $function = null): ?\Closure;
    /**
     * Sets the modified flag of the object.
     *
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    abstract public function set_modified(): \Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Adds a new or overwrite an existing list item which references the given domain item (created if it doesn't exist)
     *
     * @param string $domain Name of the domain (e.g. media, text, etc.)
     * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the new domain item
     * @param \Aimeos\MShop\Common\Item\Iface|null $refItem New item added to the given domain or null if no item should be referenced
     * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
     */
    public function add_list_item(string $domain, \Aimeos\M_Shop\Common\Item\Lists\Iface $list_item, ?\Aimeos\M_Shop\Common\Item\Iface $ref_item = null): \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface
    {
        $id = '_' . $this->list_max++;
        if ($ref_item !== null) {
            if ($ref_item instanceof \Aimeos\M_Shop\Common\Item\Domain\Iface && !$ref_item->get_domain()) {
                $ref_item->set_domain($this->get_resource_type());
            }
            $list_item->set_ref_item($ref_item)->set_ref_id($ref_item->get_id() ?: $id);
        }
        $id = $list_item->get_id() ?: $id;
        unset($this->list_items[$domain][$id]);
        // append at the end
        $this->list_items[$domain][$id] = $list_item->set_domain($domain);
        if (isset($this->list_map[$domain])) {
            unset($this->list_map[$domain][$list_item->get_type()][$list_item->get_ref_id()]);
            // append at the end
            $this->list_map[$domain][$list_item->get_type()][$list_item->get_ref_id()] = $list_item;
        }
        return $this;
    }
    /**
     * Removes a list item which references the given domain item (removed as well if it exists)
     *
     * @param string $domain Name of the domain (e.g. media, text, etc.)
     * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the domain item
     * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Existing item removed from the given domain or null if item shouldn't be removed
     * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
     */
    public function delete_list_item(string $domain, \Aimeos\M_Shop\Common\Item\Lists\Iface $list_item, ?\Aimeos\M_Shop\Common\Item\Iface $ref_item = null): \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface
    {
        if (($key = array_search($list_item, $this->list_items[$domain] ?? [], true)) !== false) {
            $this->list_rm_items[] = $list_item->set_ref_item($ref_item);
            unset($this->list_map[$domain][$list_item->get_type()][$list_item->get_ref_id()]);
            unset($this->list_items[$domain][$key]);
        }
        return $this;
    }
    /**
     * Removes a list of list items which references their domain items (removed as well if it exists)
     *
     * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $items Existing list items
     * @param bool $all True to delete referenced items as well, false for list items only
     * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
     * @throws \Aimeos\MShop\Exception If an item isn't a list item or isn't found
     */
    public function delete_list_items(iterable $items, bool $all = false): \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface
    {
        map($items)->implements(\Aimeos\M_Shop\Common\Item\Lists\Iface::class, true);
        foreach ($items as $item) {
            $ref_item = $all ? $item->get_ref_item() : null;
            $this->delete_list_item($item->get_domain(), $item, $ref_item);
        }
        return $this;
    }
    /**
     * Returns the domains for which items are available
     *
     * @return string[] List of domain names
     */
    public function get_domains(): array
    {
        return array_keys($this->list_items);
    }
    /**
     * Returns the deleted list items which include the domain items if available
     *
     * @param string|null $domain Domain name to get the deleted list items for
     * @return \Aimeos\Map Associative list of domains as keys list items as values or list items only
     */
    public function get_list_items_deleted(?string $domain = null): \Aimeos\Map
    {
        if ($domain) {
            return map($this->list_rm_items)->filter(fn($item): bool => $item->get_domain() === $domain);
        }
        return map($this->list_rm_items);
    }
    /**
     * Returns the list item for the given reference ID, domain and list type
     *
     * @param string $domain Name of the domain (e.g. product, text, etc.)
     * @param string $listtype Name of the list item type
     * @param string $refId Unique ID of the referenced item
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\MShop\Common\Item\Lists\Iface|null Matching list item or null if none
     */
    public function get_list_item(string $domain, string $listtype, string $ref_id, bool $active = true): ?\Aimeos\M_Shop\Common\Item\Lists\Iface
    {
        if (!isset($this->list_map[$domain]) && isset($this->list_items[$domain])) {
            $map = [];
            foreach ($this->list_items[$domain] as $list_item) {
                $map[$list_item->get_type()][$list_item->get_ref_id()] = $list_item;
            }
            $this->list_map[$domain] = $map;
        }
        if (isset($this->list_map[$domain][$listtype][$ref_id])) {
            $list_item = $this->list_map[$domain][$listtype][$ref_id];
            if ($active && !$list_item->is_available()) {
                return null;
            }
            return $list_item;
        }
        return null;
    }
    /**
     * Returns the list items attached, optionally filtered by domain and list type.
     *
     * The reference parameter in search() must have been set accordingly
     * to the requested domain to get the items. Otherwise, no items will be
     * returned by this method.
     *
     * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
     * @param array|string|null $listtype Name/Names of the list item type or null for all
     * @param array|string|null $type Name/Names of the item type or null for all
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
     */
    public function get_list_items($domain = null, $listtype = null, $type = null, bool $active = true): \Aimeos\Map
    {
        $result = [];
        $fcn = static::macro('listFilter');
        $iface = \Aimeos\M_Shop\Common\Item\Type_Ref\Iface::class;
        $list_types = is_array($listtype) ? $listtype : [$listtype];
        $domains = is_array($domain) ? $domain : [$domain];
        $types = is_array($type) ? $type : [$type];
        foreach ($this->list_items as $dname => $list) {
            if ($domain && !in_array($dname, $domains)) {
                continue;
            }
            $set = [];
            foreach ($list as $id => $item) {
                $ref_item = $item->get_ref_item();
                if ($type && !($ref_item && $ref_item instanceof $iface && in_array($ref_item->get_type(), $types))) {
                    continue;
                }
                if ($listtype && !in_array($item->get_type(), $list_types)) {
                    continue;
                }
                if ($active && !$item->is_available()) {
                    continue;
                }
                $set[$id] = $item;
            }
            $result = array_replace($result, $fcn ? $fcn($set) : $set);
        }
        return map($result);
    }
    /**
     * Returns the product, text, etc. items filtered by domain and optionally by type and list type.
     *
     * The reference parameter in search() must have been set accordingly
     * to the requested domain to get the items. Otherwise, no items will be
     * returned by this method.
     *
     * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
     * @param array|string|null $type Name/Names of the item type or null for all
     * @param array|string|null $listtype Name/Names of the list item type or null for all
     * @param bool $active True to return only active items, false to return all
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
     */
    public function get_ref_items($domain = null, $type = null, $listtype = null, bool $active = true): \Aimeos\Map
    {
        $list = [];
        foreach ($this->get_list_items($domain, $listtype, $type, $active) as $list_item) {
            if (($ref_item = $list_item->get_ref_item()) && (!$active || $ref_item->is_available())) {
                $list[$list_item->get_domain()][$list_item->get_ref_id()] = $ref_item;
            }
        }
        if (is_array($domain)) {
            return map($list)->only($domain);
        }
        if ($domain) {
            return map($list[$domain] ?? []);
        }
        return map($list);
    }
    /**
     * Returns the label of the item.
     * This method should be implemented in the derived class if a label column is available.
     *
     * @return string Label of the item
     */
    public function get_label(): string
    {
        return '';
    }
    /**
     * Returns the localized text type of the item or the internal label if no name is available.
     *
     * @param string $type Text type to be returned
     * @param string|null $langId Two letter ISO Language code of the text
     * @return string Specified text type or label of the item
     */
    public function get_name(string $type = 'name', ?string $lang_id = null): string
    {
        foreach ($this->get_ref_items('text', $type) as $text_item) {
            if ($text_item->get_language_id() === $lang_id || $lang_id === null) {
                return $text_item->get_content();
            }
        }
        return $this->get_label();
    }
    /**
     * Initializes the list items in the trait
     *
     * @param array $listItems Two dimensional associative list of domain / ID / list items that implement \Aimeos\MShop\Common\Item\Lists\Iface
     */
    protected function init_list_items(array $list_items)
    {
        $this->list_max = count($list_items);
        foreach ($list_items as $id => $list_item) {
            $this->list_items[$list_item->get_domain()][$id] = $list_item;
        }
    }
}