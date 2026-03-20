<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Locale
 */
namespace Aimeos\M_Shop\Locale\Item\Currency;

/**
 * Default implementation of a currency item.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Locale\Item\Currency\Iface
{
    /**
     * Sets the ID of the currency.
     *
     * @param string|null $key ID of the currency
     * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
     */
    public function set_id(?string $key): \Aimeos\M_Shop\Common\Item\Iface
    {
        return parent::set_id(\Aimeos\Utils::currency($key));
    }
    /**
     * Returns the code of the currency.
     *
     * @return string Code of the currency
     */
    public function get_code(): string
    {
        return $this->get('locale.currency.code', $this->get('locale.currency.id', ''));
    }
    /**
     * Sets the code of the currency.
     *
     * @param string $code Code of the currency
     * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('locale.currency.code', \Aimeos\Utils::currency($code, false));
    }
    /**
     * Returns the label or symbol of the currency.
     *
     * @return string Label or symbol of the currency
     */
    public function get_label(): string
    {
        return $this->get('locale.currency.label', '');
    }
    /**
     * Sets the label or symbol of the currency.
     *
     * @param string $label Label or symbol of the currency
     * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Locale\Item\Currency\Iface
    {
        return $this->set('locale.currency.label', $label);
    }
    /**
     * Returns the status of the item.
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('locale.currency.status', 1);
    }
    /**
     * Sets the status of the item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('locale.currency.status', $status);
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
     * @return \Aimeos\MShop\Locale\Item\Currency\Iface Currency item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'locale.currency.code':
                    $item->set_code($value);
                    break;
                case 'locale.currency.label':
                    $item->set_label($value);
                    break;
                case 'locale.currency.status':
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
        $list['locale.currency.code'] = $this->get_code();
        $list['locale.currency.label'] = $this->get_label();
        $list['locale.currency.status'] = $this->get_status();
        return $list;
    }
}