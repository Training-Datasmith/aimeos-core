<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Locale
 */
namespace Aimeos\M_Shop\Locale\Item\Language;

/**
 * Default implementation of a Language item.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Locale\Item\Language\Iface
{
    /**
     * Sets the id of the language.
     *
     * @param string|null $key Id to set
     * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
     */
    public function set_id(?string $key): \Aimeos\M_Shop\Common\Item\Iface
    {
        return parent::set_id(\Aimeos\Utils::language($key));
    }
    /**
     * Returns the two letter ISO language code.
     *
     * @return string two letter ISO language code
     */
    public function get_code(): string
    {
        return (string) $this->get('locale.language.code', $this->get('locale.language.id', ''));
    }
    /**
     * Sets the two letter ISO language code.
     *
     * @param string $code two letter ISO language code
     * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('locale.language.code', \Aimeos\Utils::language($code, false));
    }
    /**
     * Returns the label property.
     *
     * @return string Returns the label of the language
     */
    public function get_label(): string
    {
        return (string) $this->get('locale.language.label', '');
    }
    /**
     * Sets the label property.
     *
     * @param string $label Label of the language
     * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Locale\Item\Language\Iface
    {
        return $this->set('locale.language.label', $label);
    }
    /**
     * Returns the status of the item.
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('locale.language.status', 1);
    }
    /**
     * Sets the status of the item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('locale.language.status', $status);
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
     * @return \Aimeos\MShop\Locale\Item\Language\Iface Language item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'locale.language.code':
                    $item->set_code($value);
                    break;
                case 'locale.language.label':
                    $item->set_label($value);
                    break;
                case 'locale.language.status':
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
     * Returns the item values as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $list = parent::to_array($private);
        $list['locale.language.code'] = $this->get_code();
        $list['locale.language.label'] = $this->get_label();
        $list['locale.language.status'] = $this->get_status();
        return $list;
    }
}