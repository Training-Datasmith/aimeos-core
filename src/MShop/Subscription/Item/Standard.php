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
 * Default implementation of subscription item
 *
 * @package MShop
 * @subpackage Subscription
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Subscription\Item\Iface
{
    /**
     * Returns the associated order item
     *
     * @return \Aimeos\MShop\Order\Item\Iface|null Order item
     */
    public function get_order_item(): ?\Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->get('.orderitem');
    }
    /**
     * Returns the ID of the order
     *
     * @return string|null ID of the order
     */
    public function get_order_id(): ?string
    {
        return $this->get('subscription.orderid');
    }
    /**
     * Sets the ID of the order item which the customer bought
     *
     * @param string $id ID of the order
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_order_id(string $id): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.orderid', $id);
    }
    /**
     * Returns the ID of the ordered product
     *
     * @return string|null ID of the ordered product
     */
    public function get_order_product_id(): ?string
    {
        return $this->get('subscription.ordprodid');
    }
    /**
     * Sets the ID of the ordered product item which the customer subscribed for
     *
     * @param string $id ID of the ordered product
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_order_product_id(string $id): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.ordprodid', $id);
    }
    /**
     * Returns the date of the next subscription renewal
     *
     * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
     */
    public function get_date_next(): ?string
    {
        $value = $this->get('subscription.datenext');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets the date of the next subscription renewal
     *
     * @param string $date ISO date in "YYYY-MM-DD HH:mm:ss" format
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_date_next(string $date): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.datenext', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the date when the subscription renewal ends
     *
     * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
     */
    public function get_date_end(): ?string
    {
        $value = $this->get('subscription.dateend');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets the delivery date of the invoice.
     *
     * @param string|null $date ISO date in "YYYY-MM-DD HH:mm:ss" format
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_date_end(?string $date): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.dateend', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the time interval to pass between the subscription renewals
     *
     * @return string PHP time interval, e.g. "P1M2W"
     */
    public function get_interval(): string
    {
        return $this->get('subscription.interval', '');
    }
    /**
     * Sets the time interval to pass between the subscription renewals
     *
     * @param string $value PHP time interval, e.g. "P1M2W"
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_interval(string $value): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        if (strlen($value) > 1 && preg_match('/^P([0-9]+Y)?([0-9]+M)?([0-9]+W)?([0-9]+D)?(T?[0-9]+H)?$/', $value) !== 1) {
            throw new \Aimeos\M_Shop\Subscription\Exception(sprintf('Invalid time interval format "%1$s"', $value));
        }
        return $this->set('subscription.interval', $value);
    }
    /**
     * Returns the current renewal period of the subscription product
     *
     * @return int Current renewal period
     */
    public function get_period(): int
    {
        return $this->get('subscription.period', 1);
    }
    /**
     * Sets the current renewal period of the subscription product
     *
     * @param int $value Current renewal period
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_period(int $value): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.period', $value);
    }
    /**
     * Returns the product ID of the subscription product
     *
     * @return string Product ID
     */
    public function get_product_id(): string
    {
        return $this->get('subscription.productid', '');
    }
    /**
     * Sets the product ID of the subscription product
     *
     * @param string $value Product ID
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_product_id(string $value): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.productid', $value);
    }
    /**
     * Returns the reason for the end of the subscriptions
     *
     * @return int|null Reason code or NULL for no reason
     */
    public function get_reason(): ?int
    {
        return $this->get('subscription.reason');
    }
    /**
     * Sets the reason for the end of the subscriptions
     *
     * @param int|null $value Reason code or NULL for no reason
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_reason(?int $value): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.reason', $value);
    }
    /**
     * Returns the status of the subscriptions
     *
     * @return int Subscription status, i.e. "1" for enabled, "0" for disabled
     */
    public function get_status(): int
    {
        return $this->get('subscription.status', 1);
    }
    /**
     * Sets the status of the subscriptions
     *
     * @return int Subscription status, i.e. "1" for enabled, "0" for disabled
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        return $this->set('subscription.status', $status);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'subscription.orderid':
                    $item->set_order_id($value);
                    break;
                case 'subscription.ordprodid':
                    $item->set_order_product_id($value);
                    break;
                case 'subscription.productid':
                    $item->set_product_id($value);
                    break;
                case 'subscription.datenext':
                    $item->set_date_next($value);
                    break;
                case 'subscription.dateend':
                    $item->set_date_end($value);
                    break;
                case 'subscription.interval':
                    $item->set_interval($value);
                    break;
                case 'subscription.period':
                    $item->set_period((int) $value);
                    break;
                case 'subscription.status':
                    $item->set_status((int) $value);
                    break;
                case 'subscription.reason':
                    $item->set_reason($value !== null ? (int) $value : null);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns the item values as associative list.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $list = parent::to_array($private);
        $list['subscription.orderid'] = $this->get_order_id();
        $list['subscription.ordprodid'] = $this->get_order_product_id();
        $list['subscription.productid'] = $this->get_product_id();
        $list['subscription.datenext'] = $this->get_date_next();
        $list['subscription.dateend'] = $this->get_date_end();
        $list['subscription.interval'] = $this->get_interval();
        $list['subscription.period'] = $this->get_period();
        $list['subscription.status'] = $this->get_status();
        $list['subscription.reason'] = $this->get_reason();
        return $list;
    }
}