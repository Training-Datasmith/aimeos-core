<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package MShop
 * @subpackage Review
 */
namespace Aimeos\M_Shop\Review\Item;

/**
 * Generic interface for review items
 *
 * @package MShop
 * @subpackage Review
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Domain\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the comment for the reviewed item
     *
     * @return string Comment for the reviewed item
     */
    public function get_comment(): string;
    /**
     * Sets the new comment for the reviewed item
     *
     * @param string|null $value New comment for the reviewed item
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_comment(?string $value): \Aimeos\M_Shop\Review\Item\Iface;
    /**
     * Returns the ID of the reviewer
     *
     * @return string|null ID of the customer item
     */
    public function get_customer_id(): ?string;
    /**
     * Sets the ID of the reviewer
     *
     * @param string $value New ID of the customer item
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_customer_id(string $value): \Aimeos\M_Shop\Review\Item\Iface;
    /**
     * Returns the name of the reviewer
     *
     * @return string Name of the reviewer
     */
    public function get_name(): string;
    /**
     * Sets the new name of the reviewer
     *
     * @param string $value New name of the reviewer
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_name(string $value): \Aimeos\M_Shop\Review\Item\Iface;
    /**
     * Returns the ID of the ordered product
     *
     * @return string|null ID of the ordered product
     */
    public function get_order_product_id(): ?string;
    /**
     * Sets the ID of the ordered product item which the customer subscribed for
     *
     * @param string $value ID of the ordered product
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_order_product_id(string $value): \Aimeos\M_Shop\Review\Item\Iface;
    /**
     * Returns the rating for the reviewed item
     *
     * @return int Rating for the reviewed item (higher is better)
     */
    public function get_rating(): int;
    /**
     * Sets the new rating for the reviewed item
     *
     * @param int $value Rating for the reviewed item (higher is better)
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_rating(int $value): \Aimeos\M_Shop\Review\Item\Iface;
    /**
     * Returns the reference ID of the reviewed item, like the unique ID of a product item or a customer item
     *
     * @return string Reference ID of the common list item
     */
    public function get_ref_id(): string;
    /**
     * Sets the new reference ID of the common list item, like the unique ID of a product item or a customer item
     *
     * @param string $value New reference ID of the common list item
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_ref_id(string $value): \Aimeos\M_Shop\Review\Item\Iface;
    /**
     * Returns the response to the review
     *
     * @return string Response to the review
     */
    public function get_response(): string;
    /**
     * Sets the new response to the review
     *
     * @param string|null $value New response to the review
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_response(?string $value): \Aimeos\M_Shop\Review\Item\Iface;
}