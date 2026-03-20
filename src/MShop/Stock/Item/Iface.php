<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Stock
 */
namespace Aimeos\M_Shop\Stock\Item;

/**
 * Default stock item interface.
 *
 * @package MShop
 * @subpackage Stock
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the ID of the product the stock item belongs to.
     *
     * @return string Product ID
     */
    public function get_product_id(): string;
    /**
     * Sets a new product ID the stock item belongs to.
     *
     * @param string $value New product ID
     * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
     */
    public function set_product_id(string $value): \Aimeos\M_Shop\Stock\Item\Iface;
    /**
     * Returns the stock level.
     *
     * @return int|null Stock level
     */
    public function get_stock_level(): ?int;
    /**
     * Sets the stock level.
     *
     * @param int|null $stocklevel New stock level
     * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
     */
    public function set_stock_level($stocklevel = null): \Aimeos\M_Shop\Stock\Item\Iface;
    /**
     * Returns the back in stock date of the stock.
     *
     * @return string|null Back in stock date of the stock
     */
    public function get_date_back(): ?string;
    /**
     * Sets the stock back in stock date.
     *
     * @param string|null $dateback New back in stock date of the stock
     * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
     */
    public function set_date_back(?string $dateback): \Aimeos\M_Shop\Stock\Item\Iface;
    /**
     * Returns the expected delivery time frame
     *
     * @return string Expected delivery time frame
     */
    public function get_timeframe(): string;
    /**
     * Sets the expected delivery time frame
     *
     * @param string $timeframe Expected delivery time frame
     * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
     */
    public function set_timeframe(?string $timeframe): \Aimeos\M_Shop\Stock\Item\Iface;
}