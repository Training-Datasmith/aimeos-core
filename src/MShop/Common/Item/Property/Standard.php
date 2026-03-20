<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Property;

/**
 * Default property item implementation.
 *
 * @package MShop
 * @subpackage Common
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Common\Item\Property\Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    private ?string $langid;
    private string $prefix;
    /**
     * Initializes the property item object with the given values
     *
     * @param string $prefix Property prefix when converting to array
     * @param array $values Initial values of the list type item
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values, str_replace('.', '/', rtrim($prefix, '.')));
        $this->langid = $values['.languageid'] ?? null;
        $this->prefix = $prefix;
    }
    /**
     * Returns the unique key of the property item
     *
     * @return string Unique key consisting of type/language/value
     */
    public function get_key(): string
    {
        return substr($this->get_type() . '|' . ($this->get_language_id() ?: 'null') . '|' . $this->get_value(), 0, 255);
    }
    /**
     * Returns the language ID of the property item.
     *
     * @return string|null Language ID of the property item
     */
    public function get_language_id(): ?string
    {
        return $this->get($this->prefix . 'languageid');
    }
    /**
     *  Sets the language ID of the property item.
     *
     * @param string|null $id Language ID of the property item
     * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
     */
    public function set_language_id(?string $id): \Aimeos\M_Shop\Common\Item\Property\Iface
    {
        return $this->set($this->prefix . 'languageid', \Aimeos\Utils::language($id));
    }
    /**
     * Returns the parent id of the property item
     *
     * @return string|null Parent ID of the property item
     */
    public function get_parent_id(): ?string
    {
        return $this->get($this->prefix . 'parentid');
    }
    /**
     * Sets the new parent ID of the property item
     *
     * @param string|null $id Parent ID of the property item
     * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
     */
    public function set_parent_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set($this->prefix . 'parentid', $id);
    }
    /**
     * Returns the value of the property item.
     *
     * @return string Value of the property item
     */
    public function get_value(): string
    {
        return $this->get($this->prefix . 'value', '');
    }
    /**
     * Sets the new value of the property item.
     *
     * @param string $value Value of the property item
     * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
     */
    public function set_value(?string $value): \Aimeos\M_Shop\Common\Item\Property\Iface
    {
        return $this->set($this->prefix . 'value', (string) $value);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && ($this->langid === null || $this->get_language_id() === $this->langid || $this->get_language_id() === null);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Common\Item\Property\Iface Property item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case $this->prefix . 'parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case $this->prefix . 'languageid':
                    $item->set_language_id($value);
                    break;
                case $this->prefix . 'value':
                    $item->set_value($value);
                    break;
                case $this->prefix . 'type':
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
        $list[$this->prefix . 'languageid'] = $this->get_language_id();
        $list[$this->prefix . 'value'] = $this->get_value();
        $list[$this->prefix . 'type'] = $this->get_type();
        if ($private === true) {
            $list[$this->prefix . 'key'] = $this->get_key();
            $list[$this->prefix . 'parentid'] = $this->get_parent_id();
        }
        return $list;
    }
}