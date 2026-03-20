<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Catalog
 */
namespace Aimeos\M_Shop\Catalog\Item;

/**
 * Generic interface for catalog items.
 *
 * @package MShop
 * @subpackage Catalog
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Tree\Iface
{
    /**
     * Returns the materialized path of the catalog item.
     *
     * @return string Materialized path of the catalog item (e.g. "1.5.10.")
     */
    public function get_path_id(): string;
    /**
     * Sets a new materialized path for the catalog item.
     *
     * @param string $value New materialized path of the catalog item
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_path_id(string $value): \Aimeos\M_Shop\Catalog\Item\Iface;
    /**
     * Returns the URL segment for the catalog item.
     *
     * @return string URL segment of the catalog item
     */
    public function get_url(): string;
    /**
     * Sets a new URL segment for the catalog.
     *
     * @param string|null $url New URL segment of the catalog item
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_url(?string $url): \Aimeos\M_Shop\Catalog\Item\Iface;
    /**
     * Returns the URL target specific for that category
     *
     * @return string URL target specific for that category
     */
    public function get_target(): string;
    /**
     * Sets a new URL target specific for that category
     *
     * @param string $value New URL target specific for that category
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
     */
    public function set_target(?string $value): \Aimeos\M_Shop\Catalog\Item\Iface;
}