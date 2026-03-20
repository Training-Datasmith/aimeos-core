<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025-2026
 * @package MShop
 * @subpackage Type
 */
namespace Aimeos\M_Shop\Type\Item;

/**
 * Default implementation of the type item
 *
 * @package MShop
 * @subpackage Type
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Type\Item\Iface
{
    /**
     * Returns the code of the type item
     *
     * @return string Code of the type item
     */
    public function get_code(): string
    {
        return $this->get($this->prefix() . 'code', '');
    }
    /**
     * Sets the code of the type item
     *
     * @param string $code New code of the type item
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Type\Item\Iface
    {
        return $this->set($this->prefix() . 'code', \Aimeos\Utils::code($code));
    }
    /**
     * Returns the domain of the type item
     *
     * @return string Domain of the type item
     */
    public function get_domain(): string
    {
        return $this->get($this->prefix() . 'domain', '');
    }
    /**
     * Sets the domain of the type item
     *
     * @param string $domain New domain of the type item
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_domain(string $domain): \Aimeos\M_Shop\Type\Item\Iface
    {
        return $this->set($this->prefix() . 'domain', $domain);
    }
    /**
     * Returns the translations of the type item label
     *
     * @return array Translations of the type item label
     */
    public function get_i18n(): array
    {
        return (array) $this->get($this->prefix() . 'i18n', []);
    }
    /**
     * Sets the translations of the type item label
     *
     * @param array $value New translations of the type item label
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_i18n(array $value): \Aimeos\M_Shop\Type\Item\Iface
    {
        return $this->set($this->prefix() . 'i18n', $value);
    }
    /**
     * Returns the translated name for the type item
     *
     * @return string Translated name of the type item
     */
    public function get_name(): string
    {
        return $this->get_i18n()[$this->get('.language')] ?? $this->get_label();
    }
    /**
     * Returns the label of the type item
     *
     * @return string Label of the type item
     */
    public function get_label(): string
    {
        return $this->get($this->prefix() . 'label', '');
    }
    /**
     * Sets the label of the type item
     *
     * @param string $label New label of the type item
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Type\Item\Iface
    {
        return $this->set($this->prefix() . 'label', $label);
    }
    /**
     * Returns the position of the item in the list.
     *
     * @return int Position of the item in the list
     */
    public function get_position(): int
    {
        return $this->get($this->prefix() . 'position', 0);
    }
    /**
     * Sets the new position of the item in the list.
     *
     * @param int $pos position of the item in the list
     * @return \Aimeos\MShop\Type\Item\Iface Item for chaining method calls
     */
    public function set_position(int $pos): \Aimeos\M_Shop\Type\Item\Iface
    {
        return $this->set($this->prefix() . 'position', $pos);
    }
    /**
     * Returns the status of the type item
     *
     * @return int Status of the type item
     */
    public function get_status(): int
    {
        return $this->get($this->prefix() . 'status', 1);
    }
    /**
     * Sets the status of the type item
     *
     * @param int $status New status of the type item
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Type\Item\Iface
    {
        return $this->set($this->prefix() . 'status', $status);
    }
    /**
     * Returns the item type
     *
     * @return string Item type, subtypes are separated by slashes
     */
    public function get_resource_type(): string
    {
        return str_replace('.', '/', trim($this->prefix(), '.'));
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
     * @return \Aimeos\MShop\Type\Item\Iface Type item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Type\Item\Iface
    {
        $prefix = $this->prefix();
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case $prefix . 'code':
                    $item->set_code($value);
                    break;
                case $prefix . 'domain':
                    $item->set_domain($value);
                    break;
                case $prefix . 'i18n':
                    $item->set_i18n((array) $value);
                    break;
                case $prefix . 'label':
                    $item->set_label($value);
                    break;
                case $prefix . 'position':
                    $item->set_position((int) $value);
                    break;
                case $prefix . 'status':
                    $item->set_status((int) $value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns an associative list of item properties.
     *
     * @param bool True to return private properties, false for public only
     * @return array List of item properties.
     */
    public function to_array(bool $private = false): array
    {
        $prefix = $this->prefix();
        $list = parent::to_array($private);
        $list[$prefix . 'code'] = $this->get_code();
        $list[$prefix . 'domain'] = $this->get_domain();
        $list[$prefix . 'label'] = $this->get_label();
        $list[$prefix . 'position'] = $this->get_position();
        $list[$prefix . 'status'] = $this->get_status();
        $list[$prefix . 'i18n'] = $this->get_i18n();
        $list[$prefix . 'name'] = $this->get_name();
        return $list;
    }
}