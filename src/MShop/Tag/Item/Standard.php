<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Tag
 */
namespace Aimeos\M_Shop\Tag\Item;

/**
 * Default tag item implementation.
 *
 * @package MShop
 * @subpackage Tag
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Tag\Item\Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Returns the domain of the tag item.
     *
     * @return string Domain of the tag item
     */
    public function get_domain(): string
    {
        return $this->get('tag.domain', '');
    }
    /**
     * Sets the domain of the tag item.
     *
     * @param string $domain Domain of the tag item
     * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
     */
    public function set_domain(string $domain): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('tag.domain', $domain);
    }
    /**
     * Returns the language ID of the product tag item.
     *
     * @return string|null Language ID of the product tag item
     */
    public function get_language_id(): ?string
    {
        return $this->get('tag.languageid');
    }
    /**
     *  Sets the language ID of the product tag item.
     *
     * @param string|null $id Language ID of the product tag item
     * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
     */
    public function set_language_id(?string $id): \Aimeos\M_Shop\Tag\Item\Iface
    {
        return $this->set('tag.languageid', \Aimeos\Utils::language($id));
    }
    /**
     * Returns the label of the product tag item.
     *
     * @return string Label of the product tag item
     */
    public function get_label(): string
    {
        return $this->get('tag.label', '');
    }
    /**
     * Sets the Label of the product tag item.
     *
     * @param string $label Label of the product tag item
     * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Tag\Item\Iface
    {
        return $this->set('tag.label', $label);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_language_id() === $this->get('.languageid');
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'tag.languageid':
                    $item->set_language_id($value);
                    break;
                case 'tag.domain':
                    $item->set_domain($value);
                    break;
                case 'tag.label':
                    $item->set_label($value);
                    break;
                case 'tag.type':
                    $item->set_type($value);
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
        $list['tag.languageid'] = $this->get_language_id();
        $list['tag.domain'] = $this->get_domain();
        $list['tag.label'] = $this->get_label();
        $list['tag.type'] = $this->get_type();
        return $list;
    }
}