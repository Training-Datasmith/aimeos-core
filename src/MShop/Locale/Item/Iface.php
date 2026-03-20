<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Locale
 */
namespace Aimeos\M_Shop\Locale\Item;

/**
 * Interface of transfer objects for request specific parameters.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the site code of the item.
     *
     * Caution: This will return the site code of the locale item, not the code
     * of the associated site item! If you need the site code of the site item,
     * you have to call getSiteItem() and getCode() on the returned object.
     *
     * @return string|null Site code or NULL if not available
     */
    public function get_site_code(): ?string;
    /**
     * Returns the site item object.
     *
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Site item object
     * @throws \Aimeos\MShop\Locale\Exception if site object isn't available
     */
    public function get_site_item(): \Aimeos\M_Shop\Locale\Item\Site\Iface;
    /**
     * Returns the site IDs for the locale site constants.
     *
     * @param int $level Site level constant from \Aimeos\MShop\Locale\Manager\Base
     * @return array|string Associative list of site constant as key and sites as values or site ID
     */
    public function get_sites(int $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL);
    /**
     * Returns the list site IDs up to the root site item.
     *
     * @return array List of site IDs
     */
    public function get_site_path(): array;
    /**
     * Sets the identifier of the shop instance.
     *
     * @param string $id ID of the shop instance
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     */
    public function set_site_id(string $id): \Aimeos\M_Shop\Locale\Item\Iface;
    /**
     * Returns the ISO language code.
     *
     * @return string|null ISO language code (e.g. de or de_DE)
     */
    public function get_language_id(): ?string;
    /**
     * Sets the ISO language code.
     *
     * @param string|null $langid ISO language code (e.g. de or de_DE)
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     */
    public function set_language_id(?string $langid): \Aimeos\M_Shop\Locale\Item\Iface;
    /**
     * Returns the currency ID.
     *
     * @return string|null Three letter ISO currency code (e.g. EUR)
     */
    public function get_currency_id(): ?string;
    /**
     * Sets the currency ID.
     *
     * @param string|null $currencyid Three letter ISO currency code (e.g. EUR)
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     */
    public function set_currency_id(?string $currencyid): \Aimeos\M_Shop\Locale\Item\Iface;
}