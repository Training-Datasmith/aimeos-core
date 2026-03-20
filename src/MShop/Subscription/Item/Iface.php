<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Subscription
 */
namespace Aimeos\M_Shop\Subscription\Item;

/**
 * Interface for all order item implementations.
 *
 * @package MShop
 * @subpackage Subscription
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface
{
    /**
     * Renewing payment failed
     */
    public const REASON_PAYMENT = -1;
    /**
     * Subscription ended normally
     */
    public const REASON_END = 0;
    /**
     * Subscription cancelled by customer
     */
    public const REASON_CANCEL = 1;
    /**
     * Returns the associated order item
     *
     * @return \Aimeos\MShop\Order\Item\Iface|null Order item
     */
    public function get_order_item(): ?\Aimeos\M_Shop\Order\Item\Iface;
    /**
     * Returns the ID of the order
     *
     * @return string|null ID of the order
     */
    public function get_order_id(): ?string;
    /**
     * Sets the ID of the order item which the customer bought
     *
     * @param string $id ID of the order
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_order_id(string $id): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the ID of the ordered product
     *
     * @return string|null ID of the ordered product
     */
    public function get_order_product_id(): ?string;
    /**
     * Sets the ID of the ordered product item which the customer subscribed for
     *
     * @param string $id ID of the ordered product
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_order_product_id(string $id): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the date of the next subscription renewal
     *
     * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
     */
    public function get_date_next(): ?string;
    /**
     * Sets the date of the next subscription renewal
     *
     * @param string $date ISO date in "YYYY-MM-DD HH:mm:ss" format
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_date_next(string $date): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the date when the subscription renewal ends
     *
     * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
     */
    public function get_date_end(): ?string;
    /**
     * Sets the delivery date of the invoice.
     *
     * @param string|null $date ISO date in "YYYY-MM-DD HH:mm:ss" format
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_date_end(?string $date): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the time interval to pass between the subscription renewals
     *
     * @return string PHP time interval, e.g. "P1M2W"
     */
    public function get_interval(): string;
    /**
     * Sets the time interval to pass between the subscription renewals
     *
     * @param string $value PHP time interval, e.g. "P1M2W"
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_interval(string $value): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the current renewal period of the subscription product
     *
     * @return int Current renewal period
     */
    public function get_period(): int;
    /**
     * Sets the current renewal period of the subscription product
     *
     * @param int $value Current renewal period
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_period(int $value): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the product ID of the subscription product
     *
     * @return string Product ID
     */
    public function get_product_id(): string;
    /**
     * Sets the product ID of the subscription product
     *
     * @param string $value Product ID
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_product_id(string $value): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the reason for the end of the subscriptions
     *
     * @return int|null Reason code or NULL for no reason
     */
    public function get_reason(): ?int;
    /**
     * Sets the reason for the end of the subscriptions
     *
     * @param int|null Reason code or NULL for no reason
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_reason(?int $status): \Aimeos\M_Shop\Subscription\Item\Iface;
    /**
     * Returns the status of the subscriptions
     *
     * @return int Subscription status, i.e. "1" for enabled, "0" for disabled
     */
    public function get_status(): int;
    /**
     * Sets the status of the subscriptions
     *
     * @param int Subscription status, i.e. "1" for enabled, "0" for disabled
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Subscription\Item\Iface;
}