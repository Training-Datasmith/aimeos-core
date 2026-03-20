<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Product
 */
namespace Aimeos\M_Shop\Product\Item;

/**
 * Generic interface for product items created and saved by product managers.
 *
 * @package MShop
 * @subpackage Product
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Property_Ref\Iface, \Aimeos\M_Shop\Common\Item\Rating\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Time\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the parent product items referencing the product
     *
     * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Product\Item\Iface
     */
    public function get_parent_items(): \Aimeos\Map;
    /**
     * Returns the supplier items referencing the product
     *
     * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Supplier\Item\Iface
     */
    public function get_site_item(): ?\Aimeos\M_Shop\Locale\Item\Site\Iface;
    /**
     * Adds a new stock item or overwrite an existing one
     *
     * @param \Aimeos\MShop\Stock\Item\Iface $item New or existing stock item
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function add_stock_item(\Aimeos\M_Shop\Stock\Item\Iface $item): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Adds new stock items or overwrite existing ones
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface $item New or existing stock item
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function add_stock_items(iterable $items): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Removes an existing stock item
     *
     * @param \Aimeos\MShop\Stock\Item\Iface $item Existing stock item
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function delete_stock_item(\Aimeos\M_Shop\Stock\Item\Iface $item): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Removes a list of existing stock items
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface[] $items Existing stock items
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function delete_stock_items(iterable $items): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the deleted stock items
     *
     * @return \Aimeos\Map Stock items implementing \Aimeos\MShop\Stock\Item\Iface
     */
    public function get_stock_items_deleted(): \Aimeos\Map;
    /**
     * Returns the stock items associated to the product
     *
     * @param string|null $type Type of the stock item
     * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Stock\Item\Iface
     */
    public function get_stock_items($type = null): \Aimeos\Map;
    /**
     * Adds a new stock item or overwrite an existing one
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface[] $items New list of stock items
     * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
     */
    public function set_stock_items(iterable $items): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the code of the product item.
     *
     * @return string Code of the product
     */
    public function get_code(): string;
    /**
     * Sets a new code of the product item.
     *
     * @param string $code New code of the product item
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the data set name assigned to the product item.
     *
     * @return string Data set name
     */
    public function get_dataset(): string;
    /**
     * Sets a new data set name assignd to the product item.
     *
     * @param string $name New data set name
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_dataset(?string $name): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the label of the product item.
     *
     * @return string Label of the product item
     */
    public function get_label(): string;
    /**
     * Sets a new label of the product.
     *
     * @param string $label New label of the product item
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the URL segment for the product item.
     *
     * @return string URL segment of the product item
     */
    public function get_url(): string;
    /**
     * Sets a new URL segment for the product.
     *
     * @param string|null $url New URL segment of the product item
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_url(?string $url): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the quantity scale of the product item.
     *
     * @return float Quantity scale
     */
    public function get_scale(): float;
    /**
     * Sets a new quantity scale of the product item.
     *
     * @param float $value New quantity scale
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_scale(float $value): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the URL target specific for that product
     *
     * @return string URL target specific for that product
     */
    public function get_target(): string;
    /**
     * Sets a new label of the product item.
     *
     * @param string $value New URL target specific for that product
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_target(?string $value): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the flag if stock is available for that product.
     *
     * @return int "1" if product is in stock, "0" if product is out of stock
     */
    public function in_stock(): int;
    /**
     * Sets the flag if stock is available for that product.
     *
     * @param int $value "1" if product is in stock, "0" if product is out of stock
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_in_stock(int $value): \Aimeos\M_Shop\Product\Item\Iface;
    /**
     * Returns the boost factor for that product.
     *
     * @return float Boost factor
     */
    public function boost(): float;
    /**
     * Sets the boost factor for that product.
     *
     * @param float $value Boost factor
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_boost(float $value): \Aimeos\M_Shop\Product\Item\Iface;
}