<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2026
 * @package MShop
 * @subpackage Basket
 */
namespace Aimeos\M_Shop\Basket\Item;

/**
 * Generic interface for baskets.
 *
 * @package MShop
 * @subpackage Basket
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface
{
    /**
     * Returns the basket object.
     *
     * @return \Aimeos\MShop\Order\Item\Iface|null $basket Basket object
     */
    public function get_item(): ?\Aimeos\M_Shop\Order\Item\Iface;
    /**
     * Sets the basket object.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return \Aimeos\MShop\Basket\Item\Iface Basket item for chaining method calls
     */
    public function set_item(\Aimeos\M_Shop\Order\Item\Iface $basket): \Aimeos\M_Shop\Basket\Item\Iface;
    /**
     * Returns the ID of the customer who owns the basket.
     *
     * @return string Unique ID of the customer
     */
    public function get_customer_id(): string;
    /**
     * Sets the ID of the customer who owned the basket.
     *
     * @param string $customerid Unique ID of the customer
     * @return \Aimeos\MShop\Basket\Item\Iface Basket item for chaining method calls
     */
    public function set_customer_id(?string $customerid): \Aimeos\M_Shop\Basket\Item\Iface;
    /**
     * Returns the name of the basket.
     *
     * @return string Name for the basket
     */
    public function get_name(): string;
    /**
     * Sets the name of the basket.
     *
     * @param string $name Name for the basket
     * @return \Aimeos\MShop\Basket\Item\Iface Basket item for chaining method calls
     */
    public function set_name(?string $name): \Aimeos\M_Shop\Basket\Item\Iface;
}