<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Catalog
 */
namespace Aimeos\M_Shop\Catalog\Item;

use Aimeos\M_Shop\Common\Item\Config;
use Aimeos\M_Shop\Common\Item\Lists_Ref;
/**
 * Generic interface for catalog items.
 *
 * @package MShop
 * @subpackage Catalog
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Catalog\Item\Iface
{
    use Config\Traits, Lists_Ref\Traits {
        Lists_Ref\Traits::__clone as __cloneList;
        Lists_Ref\Traits::getName as getNameList;
    }
    private array $deleted_items = [];
    private array $children;
    /**
     * Initializes the catalog item.
     *
     * @param \Aimeos\MW\Tree\Node\Iface $node Tree node
     * @param array $values Assoicative list of key/value pairs
     * @param \Aimeos\MShop\Catalog\Item\Iface[] $children List of children of the item
     * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
     */
    public function __construct(private \Aimeos\MW\Tree\Node\Iface $node, array $values = [], array $children = [], array $list_items = [])
    {
        parent::__construct('', $values);
        map($children)->implements(\Aimeos\M_Shop\Catalog\Item\Iface::class, true);
        $this->init_list_items($list_items);
        $this->children = $children;
    }
    /**
     * Clones internal objects of the catalog item.
     */
    public function __clone()
    {
        parent::__clone();
        $this->__clone_list();
        $this->node = clone $this->node;
    }
    /**
     * Tests if the item property for the given name is available
     *
     * @param string $name Name of the property
     * @return bool True if the property exists, false if not
     */
    public function __isset(string $name): bool
    {
        if ($name === 'children') {
            return true;
        }
        return parent::__isset($name) ?: isset($this->node->{$name});
    }
    /**
     * Returns the item property for the given name
     *
     * @param string $name Name of the property
     * @param mixed $default Default value if property is unknown
     * @return mixed|null Property value or default value if property is unknown
     */
    public function get(string $name, $default = null)
    {
        if ($name === 'children') {
            return $this->children;
        }
        if (($value = parent::get($name)) !== null) {
            return $value;
        }
        return $this->node->{$name} ?? $default;
    }
    /**
     * Returns the unique ID of the node.
     *
     * @return string|null Unique ID of the node
     */
    public function get_id(): ?string
    {
        return $this->node->get_id();
    }
    /**
     * Sets the unique ID of the node.
     *
     * @param string|null $id Unique ID of the node
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_id(?string $id = null): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->node->set_id($id);
        return $this;
    }
    /**
     * Returns the site ID of the item.
     *
     * @return string Site ID of the item
     */
    public function get_site_id(): string
    {
        return (string) $this->node->siteid;
    }
    /**
     * Returns the internal name of the item.
     *
     * @return string Name of the item
     */
    public function get_label(): string
    {
        return $this->node->get_label();
    }
    /**
     * Sets the new internal name of the item.
     *
     * @param string $name New name of the item
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_label(string $name): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        $this->node->set_label($name);
        return $this;
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
        $name = $this->get_name_list($type, $lang_id);
        if ($type === 'url' && $name === $this->get_label()) {
            return $this->get_url();
        }
        return $name;
    }
    /**
     * Returns the materialized path of the catalog item.
     *
     * @return string Materialized path of the catalog item (e.g. "1.5.10.")
     */
    public function get_path_id(): string
    {
        return (string) ($this->node->pathid ?? '');
    }
    /**
     * Sets a new materialized path for the catalog item.
     *
     * @param string $value New materialized path of the catalog item
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_path_id(string $value): \Aimeos\M_Shop\Catalog\Item\Iface
    {
        $this->node->pathid = $value;
        return $this;
    }
    /**
     * Returns the URL segment for the catalog item.
     *
     * @return string URL segment of the catalog item
     */
    public function get_url(): string
    {
        return (string) ($this->node->url ?: \Aimeos\Base\Str::slug($this->get_label()));
    }
    /**
     * Sets a new URL segment for the catalog.
     *
     * @param string|null $url New URL segment of the catalog item
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_url(?string $url): \Aimeos\M_Shop\Catalog\Item\Iface
    {
        $this->node->url = (string) $url;
        return $this;
    }
    /**
     * Returns the code of the item.
     *
     * @return string Code of the item
     */
    public function get_code(): string
    {
        return $this->node->get_code();
    }
    /**
     * Sets the new code of the item.
     *
     * @param string $code New code of the item
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        $this->node->set_code(\Aimeos\Utils::code($code));
        return $this;
    }
    /**
     * Returns the status of the item.
     *
     * @return int Greater than zero if enabled, zero or negative values if disabled
     */
    public function get_status(): int
    {
        return $this->node->get_status();
    }
    /**
     * Sets the new status of the item.
     *
     * @param int $status True if enabled, false if not
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->node->set_status($status);
        return $this;
    }
    /**
     * Returns the URL target specific for that category
     *
     * @return string URL target specific for that category
     */
    public function get_target(): string
    {
        return (string) $this->node->target;
    }
    /**
     * Sets a new URL target specific for that category
     *
     * @param string $value New URL target specific for that category
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_target(?string $value): \Aimeos\M_Shop\Catalog\Item\Iface
    {
        $this->node->target = (string) $value;
        return $this;
    }
    /**
     * Returns modify date/time of the order item base product.
     *
     * @return string|null Returns modify date/time of the order base item
     */
    public function get_time_modified(): ?string
    {
        return $this->node->mtime;
    }
    /**
     * Returns the create date of the item.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_time_created(): ?string
    {
        return $this->node->ctime;
    }
    /**
     * Returns the editor code of editor who created/modified the item at last.
     *
     * @return string Editor who created/modified the item at last
     */
    public function editor(): string
    {
        return (string) $this->node->editor;
    }
    /**
     * Adds a child node to this node.
     *
     * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
     */
    public function add_child(\Aimeos\M_Shop\Common\Item\Tree\Iface $item): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        // don't set the modified flag as it's only for the values
        $this->children[] = $item;
        return $this;
    }
    /**
     * Removes a child node from this node.
     *
     * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to remove
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
     */
    public function delete_child(\Aimeos\M_Shop\Common\Item\Tree\Iface $item): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        foreach ($this->children as $idx => $child) {
            if ($child === $item) {
                $this->deleted_items[] = $item;
                unset($this->children[$idx]);
            }
        }
        return $this;
    }
    /**
     * Returns a child of this node identified by its index.
     *
     * @param int $index Index of child node
     * @return \Aimeos\MShop\Catalog\Item\Iface Selected node
     */
    public function get_child(int $index): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        if (isset($this->children[$index])) {
            return $this->children[$index];
        }
        throw new \Aimeos\M_Shop\Catalog\Exception(sprintf('Child node with index "%1$d" not available', $index));
    }
    /**
     * Returns all children of this node.
     *
     * @return \Aimeos\Map Numerically indexed list of children implementing \Aimeos\MShop\Catalog\Item\Iface
     */
    public function get_children(): \Aimeos\Map
    {
        return map($this->children);
    }
    /**
     * Returns the deleted children.
     *
     * @return \Aimeos\Map List of removed children implementing \Aimeos\MShop\Catalog\Item\Iface
     */
    public function get_children_deleted(): \Aimeos\Map
    {
        return map($this->deleted_items);
    }
    /**
     * Tests if a node has children.
     *
     * @return bool True if node has children, false if not
     */
    public function has_children(): bool
    {
        if (count($this->children) > 0) {
            return true;
        }
        return $this->node->has_children();
    }
    /**
     * Returns the internal node.
     *
     * For internal use only!
     *
     * @return \Aimeos\MW\Tree\Node\Iface Internal node object
     */
    public function get_node(): \Aimeos\MW\Tree\Node\Iface
    {
        return $this->node;
    }
    /**
     * Returns the level of the item in the tree
     *
     * For internal use only!
     *
     * @return int Level of the item starting with "0" for the root node
     */
    public function get_level(): int
    {
        return $this->node->level ?: 0;
    }
    /**
     * Returns the ID of the parent category
     *
     * For internal use only!
     *
     * @return string|null Unique ID of the parent category
     */
    public function get_parent_id(): ?string
    {
        return $this->node->parentid;
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_status() > 0;
    }
    /**
     * Checks, whether this node was modified.
     *
     * @return bool True if the content of the node is modified, false if not
     */
    public function is_modified(): bool
    {
        if (parent::is_modified()) {
            return true;
        }
        return $this->node->is_modified();
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'catalog.url':
                    $item->set_url($value);
                    break;
                case 'catalog.code':
                    $item->set_code($value);
                    break;
                case 'catalog.label':
                    $item->set_label($value);
                    break;
                case 'catalog.target':
                    $item->set_target($value);
                    break;
                case 'catalog.status':
                    $item->set_status((int) $value);
                    break;
                case 'catalog.config':
                    $item->set_config((array) $value);
                    break;
                case 'catalog.pathid':
                    !$private ?: $item->set_path_id($value);
                    break;
                case 'catalog.id':
                    !$private ?: $item->set_id($value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns the public values of the node as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Assciative list of key/value pairs
     */
    public function to_array(bool $private = false): array
    {
        $list = ['catalog.url' => $this->get_url(), 'catalog.code' => $this->get_code(), 'catalog.label' => $this->get_label(), 'catalog.config' => $this->get_config(), 'catalog.status' => $this->get_status(), 'catalog.target' => $this->get_target(), 'catalog.hasChildren' => $this->has_children()];
        if ($private === true) {
            $list['catalog.id'] = $this->get_id();
            $list['catalog.level'] = $this->get_level();
            $list['catalog.pathid'] = $this->get_path_id();
            $list['catalog.siteid'] = $this->get_site_id();
            $list['catalog.parentid'] = $this->get_parent_id();
            $list['catalog.ctime'] = $this->get_time_created();
            $list['catalog.mtime'] = $this->get_time_modified();
            $list['catalog.editor'] = $this->editor();
        }
        return $list;
    }
    /**
     * Returns the node and its children as list
     *
     * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Catalog\Item\Iface
     */
    public function to_list(): \Aimeos\Map
    {
        $list = map([$this->get_id() => $this]);
        foreach ($this->get_children() as $child) {
            $list = $list->union($child->to_list());
        }
        return $list;
    }
}