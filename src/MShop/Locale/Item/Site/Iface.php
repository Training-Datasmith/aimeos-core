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
 * Common interface for all Site items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Rating\Iface, \Aimeos\M_Shop\Common\Item\Tree\Iface
{
    /**
     * Sets the ID of the site.
     *
     * @param string $value Unique ID of the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Locale\Item\Site\Iface;
    /**
     * Returns the icon path of the site.
     *
     * @return string Returns the icon of the site
     */
    public function get_icon(): string;
    /**
     * Sets the icon path of the site.
     *
     * @param string $value The icon of the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_icon(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Returns the logo path of the site.
     *
     * @param bool $large Return the largest image instead of the smallest
     * @return string Returns the logo of the site
     */
    public function get_logo(bool $large = false): string;
    /**
     * Returns the logo path of the site.
     *
     * @return string Returns the logo of the site
     */
    public function get_logos(): array;
    /**
     * Sets the logo path of the site.
     *
     * @param string $value The logo of the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_logo(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Sets the logo path of the site.
     *
     * @param array $value List of logo URLs with widths of the media file in pixels as keys
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_logos(array $value): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Returns the ID of the referenced customer/supplier related to the site.
     *
     * @return string Returns the referenced customer/supplier ID related to the site
     */
    public function get_ref_id(): string;
    /**
     * Sets the ID of the referenced customer/supplier related to the site.
     *
     * @param string $value The referenced customer/supplier ID related to the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_ref_id(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface;
    /**
     * Returns the theme name for the site.
     *
     * @return string|null Returns the theme name for the site or empty for default theme
     */
    public function get_theme(): ?string;
    /**
     * Sets the theme name for the site.
     *
     * @param string $value The theme name for the site
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
     */
    public function set_theme(string $value): \Aimeos\M_Shop\Common\Item\Tree\Iface;
}