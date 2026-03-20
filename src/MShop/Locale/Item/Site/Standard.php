<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Locale
 */
namespace Aimeos\M_Shop\Locale\Item\Site;

/**
 * Default implementation of a Site item.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Locale\Item\Site\Iface
{
    use \Aimeos\M_Shop\Common\Item\Config\Traits;
    /**
     * Returns the ID of the site.
     *
     * @return string Unique ID of the site
     */
    public function get_site_id(): string
    {
        return $this->get('locale.site.siteid', '');
    }
    /**
     * Sets the ID of the site.
     *
     * @param string $value Unique ID of the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Locale\Item\Site\Iface
    {
        return $this->set('locale.site.siteid', $value);
    }
    /**
     * Returns the code of the site.
     *
     * @return string Returns the code of the item
     */
    public function get_code(): string
    {
        return $this->get('locale.site.code', '');
    }
    /**
     * Sets the code of the site.
     *
     * @param string $code The code to set
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        return $this->set('locale.site.code', \Aimeos\Utils::code($code, 255));
    }
    /**
     * Returns the label property of the site.
     *
     * @return string Returns the label of the Site
     */
    public function get_label(): string
    {
        return $this->get('locale.site.label', '');
    }
    /**
     * Sets the label property of the site.
     *
     * @param string $label The label of the Site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        return $this->set('locale.site.label', $label);
    }
    /**
     * Returns the level of the item in the tree
     *
     * @return int Level of the item starting with "0" for the root node
     */
    public function get_level(): int
    {
        return 0;
    }
    /**
     * Returns the logo path of the site.
     *
     * @param bool $large Return the largest image instead of the smallest
     * @return string Returns the logo of the site
     */
    public function get_logo(bool $large = false): string
    {
        if (($list = (array) $this->get('locale.site.logo', [])) !== []) {
            return (string) ($large ? end($list) : current($list));
        }
        return '';
    }
    /**
     * Returns the logo path of the site.
     *
     * @return string Returns the logo of the site
     */
    public function get_logos(): array
    {
        return (array) $this->get('locale.site.logo', []);
    }
    /**
     * Returns the icon path of the site.
     *
     * @return string Returns the icon of the site
     */
    public function get_icon(): string
    {
        return $this->get('locale.site.icon', '');
    }
    /**
     * Sets the icon path of the site.
     *
     * @param string $value The icon of the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_icon(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        return $this->set('locale.site.icon', $value);
    }
    /**
     * Sets the logo path of the site.
     *
     * @param string $value The logo of the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_logo(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        return $this->set('locale.site.logo', [1 => $value]);
    }
    /**
     * Sets the logo path of the site.
     *
     * @param array $value List of logo URLs with widths of the media file in pixels as keys
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_logos(array $value): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        return $this->set('locale.site.logo', $value);
    }
    /**
     * Returns the ID of the parent site
     *
     * @return string Unique ID of the parent site
     */
    public function get_parent_id(): string
    {
        return '0';
    }
    /**
     * Returns the rating of the item
     *
     * @return string Decimal value of the item rating
     */
    public function get_rating(): string
    {
        return (string) $this->get('locale.site.rating', 0);
    }
    /**
     * Returns the total number of ratings for the item
     *
     * @return int Total number of ratings for the item
     */
    public function get_ratings(): int
    {
        return (int) $this->get('locale.site.ratings', 0);
    }
    /**
     * Returns the ID of the referenced customer/supplier related to the site.
     *
     * @return string Returns the referenced customer/supplier ID related to the site
     */
    public function get_ref_id(): string
    {
        return $this->get('locale.site.refid', '');
    }
    /**
     * Sets the ID of the referenced customer/supplier related to the site.
     *
     * @param string $value The referenced customer/supplier ID related to the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_ref_id(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        return $this->set('locale.site.refid', $value);
    }
    /**
     * Returns the status property of the Site.
     *
     * @return int Returns the status of the Site
     */
    public function get_status(): int
    {
        return $this->get('locale.site.status', 1);
    }
    /**
     * Sets status property.
     *
     * @param int $status The status of the Site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('locale.site.status', $status);
    }
    /**
     * Returns the theme name for the site.
     *
     * @return string|null Returns the theme name for the site or emtpy for default theme
     */
    public function get_theme(): ?string
    {
        return $this->get('locale.site.theme');
    }
    /**
     * Sets the theme name for the site.
     *
     * @param string $value The theme name for the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_theme(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        return $this->set('locale.site.theme', $value);
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
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Site item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'locale.site.code':
                    $item->set_code($value);
                    break;
                case 'locale.site.label':
                    $item->set_label($value);
                    break;
                case 'locale.site.status':
                    $item->set_status((int) $value);
                    break;
                case 'locale.site.config':
                    $item->set_config((array) $value);
                    break;
                case 'locale.site.refid':
                    $item->set_ref_id($value);
                    break;
                case 'locale.site.logo':
                    $item->set_logos((array) $value);
                    break;
                case 'locale.site.theme':
                    $item->set_theme($value);
                    break;
                case 'locale.site.icon':
                    $item->set_icon($value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns the item values as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $list = parent::to_array($private);
        $list['locale.site.code'] = $this->get_code();
        $list['locale.site.icon'] = $this->get_icon();
        $list['locale.site.logo'] = $this->get_logos();
        $list['locale.site.theme'] = $this->get_theme();
        $list['locale.site.label'] = $this->get_label();
        $list['locale.site.status'] = $this->get_status();
        $list['locale.site.refid'] = $this->get_ref_id();
        $list['locale.site.hasChildren'] = $this->has_children();
        $list['locale.site.ratings'] = $this->get_ratings();
        $list['locale.site.rating'] = $this->get_rating();
        if ($private === true) {
            $list['locale.site.level'] = $this->get_level();
            $list['locale.site.parentid'] = $this->get_parent_id();
            $list['locale.site.config'] = $this->get_config();
        }
        return $list;
    }
    /**
     * Adds a child node to this node.
     *
     * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
     */
    public function add_child(\Aimeos\M_Shop\Common\Item\Tree\Iface $item): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
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
        return $this;
    }
    /**
     * Returns a child of this node identified by its index.
     *
     * @param int $index Index of child node
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Selected node
     */
    public function get_child(int $index): \Aimeos\M_Shop\Common\Item\Tree\Iface
    {
        throw new \Aimeos\M_Shop\Locale\Exception(sprintf('Child node with index "%1$d" not available', $index));
    }
    /**
     * Returns all children of this node.
     *
     * @return \Aimeos\Map Numerically indexed list of items implementing \Aimeos\MShop\Locale\Item\Site\Iface
     */
    public function get_children(): \Aimeos\Map
    {
        return map();
    }
    /**
     * Returns the deleted children.
     *
     * @return \Aimeos\Map List of removed children implementing \Aimeos\MShop\Locale\Item\Site\Iface
     */
    public function get_children_deleted(): \Aimeos\Map
    {
        return map();
    }
    /**
     * Tests if a node has children.
     *
     * @return bool True if node has children, false if not
     */
    public function has_children(): bool
    {
        return false;
    }
    /**
     * Returns the node and its children as list
     *
     * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Locale\Item\Site\Iface
     */
    public function to_list(): \Aimeos\Map
    {
        return map([$this->get_id() => $this]);
    }
}