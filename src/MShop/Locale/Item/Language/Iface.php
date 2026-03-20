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
 * Common interface for all language items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the two letter ISO language code.
     *
     * @return string two letter ISO language code
     */
    public function get_code(): string;
    /**
     * Sets the two letter ISO language code.
     *
     * @param string $key two letter ISO language code
     * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
     */
    public function set_code(string $key): \Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Returns the label property of the language.
     *
     * @return string Label or symbol of the language
     */
    public function get_label(): string;
    /**
     * Sets the label property of the language.
     *
     * @param string $label Label or symbol of the language
     * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Locale\Item\Language\Iface;
}