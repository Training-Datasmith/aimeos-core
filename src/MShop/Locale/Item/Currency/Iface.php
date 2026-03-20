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
 * Common interface for all currency items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the code of the currency.
     *
     * @return string Code of the currency
     */
    public function get_code(): string;
    /**
     * Sets the code of the currency.
     *
     * @param string $key Code of the currency
     * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
     */
    public function set_code(string $key): \Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Returns the label or symbol of the currency.
     *
     * @return string Label or symbol of the currency
     */
    public function get_label(): string;
    /**
     * Sets the label or symbol of the currency.
     *
     * @param string $label Label or symbol of the currency
     * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Locale\Item\Currency\Iface;
}