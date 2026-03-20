<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item;

/**
 * Default implementation of an order invoice item.
 *
 * @property int oldPaymentStatus Last delivery status before it was changed by setDeliveryStatus()
 * @property int oldDeliveryStatus Last payment status before it was changed by setPaymentStatus()
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Order\Item\Base implements \Aimeos\M_Shop\Order\Item\Iface
{
    /**
     * Returns the order number
     *
     * @return string Order number
     */
    public function get_order_number(): string
    {
        if (self::macro('ordernumber')) {
            return (string) $this->call('ordernumber');
        }
        return (string) $this->get_id();
    }
    /**
     * Returns the number of the invoice.
     *
     * @return string Invoice number
     */
    public function get_invoice_number(): string
    {
        if (self::macro('invoicenumber')) {
            return (string) $this->call('invoicenumber');
        }
        return (string) $this->get('order.invoiceno', '');
    }
    /**
     * Sets the number of the invoice.
     *
     * @param string|null $value Invoice number
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     */
    public function set_invoice_number(?string $value): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('order.invoiceno', (string) $value);
    }
    /**
     * Returns the channel of the invoice (repeating, web, phone, etc).
     *
     * @return string Invoice channel
     */
    public function get_channel(): string
    {
        return (string) $this->get('order.channel', '');
    }
    /**
     * Sets the channel of the invoice.
     *
     * @param string|null $channel Invoice channel
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     */
    public function set_channel(?string $channel): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('order.channel', \Aimeos\Utils::code((string) $channel));
    }
    /**
     * Returns the delivery date of the invoice.
     *
     * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
     */
    public function get_date_delivery(): ?string
    {
        $value = $this->get('order.datedelivery');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets the delivery date of the invoice.
     *
     * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     */
    public function set_date_delivery(?string $date): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->set('order.datedelivery', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the purchase date of the invoice.
     *
     * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
     */
    public function get_date_payment(): ?string
    {
        $value = $this->get('order.datepayment');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets the purchase date of the invoice.
     *
     * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     */
    public function set_date_payment(?string $date): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->set('order.datepayment', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the delivery status of the invoice.
     *
     * @return int Status code constant from \Aimeos\MShop\Order\Item\Base
     */
    public function get_status_delivery(): int
    {
        return $this->get('order.statusdelivery', -1);
    }
    /**
     * Sets the delivery status of the invoice.
     *
     * @param int $status Status code constant from \Aimeos\MShop\Order\Item\Base
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     */
    public function set_status_delivery(int $status): \Aimeos\M_Shop\Order\Item\Iface
    {
        $this->set('.statusdelivery', $this->get('order.statusdelivery'));
        return $this->set('order.statusdelivery', $status);
    }
    /**
     * Returns the payment status of the invoice.
     *
     * @return int Payment constant from \Aimeos\MShop\Order\Item\Base
     */
    public function get_status_payment(): int
    {
        return $this->get('order.statuspayment', -1);
    }
    /**
     * Sets the payment status of the invoice.
     *
     * @param int $status Payment constant from \Aimeos\MShop\Order\Item\Base
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     */
    public function set_status_payment(int $status): \Aimeos\M_Shop\Order\Item\Iface
    {
        if ($status !== $this->get_status_payment()) {
            $this->set('order.datepayment', date('Y-m-d H:i:s'));
        }
        $this->set('.statuspayment', $this->get('order.statuspayment'));
        return $this->set('order.statuspayment', $status);
    }
    /**
     * Returns the related invoice ID.
     *
     * @return string Related invoice ID
     */
    public function get_related_id(): string
    {
        return (string) $this->get('order.relatedid', '');
    }
    /**
     * Sets the related invoice ID.
     *
     * @param string|null $id Related invoice ID
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     * @throws \Aimeos\MShop\Order\Exception If ID is invalid
     */
    public function set_related_id(?string $id): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->set('order.relatedid', (string) $id);
    }
    /**
     * Returns the associated customer item
     *
     * @return \Aimeos\MShop\Customer\Item\Iface|null Customer item
     */
    public function get_customer_item(): ?\Aimeos\M_Shop\Customer\Item\Iface
    {
        return $this->customer;
    }
    /**
     * Returns the code of the site the item is stored.
     *
     * @return string Site code (or empty string if not available)
     */
    public function get_site_code(): string
    {
        return $this->get('order.sitecode', '');
    }
    /**
     * Returns the comment field of the order item.
     *
     * @return string Comment for the order
     */
    public function get_comment(): string
    {
        return $this->get('order.comment', '');
    }
    /**
     * Sets the comment field of the order item
     *
     * @param string|null $comment Comment for the order
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
     */
    public function set_comment(?string $comment): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->set('order.comment', (string) $comment);
    }
    /**
     * Returns the customer ID of the customer who has ordered.
     *
     * @return string Unique ID of the customer
     */
    public function get_customer_id(): string
    {
        return $this->get('order.customerid', '');
    }
    /**
     * Sets the customer ID of the customer who has ordered.
     *
     * @param string|null $customerid Unique ID of the customer
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
     */
    public function set_customer_id(?string $customerid): \Aimeos\M_Shop\Order\Item\Iface
    {
        if ((string) $customerid !== $this->get_customer_id()) {
            $this->notify('setCustomerId.before', (string) $customerid);
            $this->set('order.customerid', (string) $customerid);
            $this->notify('setCustomerId.after', (string) $customerid);
        }
        return $this;
    }
    /**
     * Returns the customer reference field of the order item
     *
     * @return string Customer reference for the order
     */
    public function get_customer_reference(): string
    {
        return (string) $this->get('order.customerref', '');
    }
    /**
     * Sets the customer reference field of the order item
     *
     * @param string|null $value Customer reference for the order
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
     */
    public function set_customer_reference(?string $value): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->set('order.customerref', (string) $value);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.channel':
                    !$private ?: $item->set_channel($value);
                    break;
                case 'order.invoiceno':
                    !$private ?: $item->set_invoice_number($value);
                    break;
                case 'order.statusdelivery':
                    !$private ?: $item->set_status_delivery((int) $value);
                    break;
                case 'order.statuspayment':
                    !$private ?: $item->set_status_payment((int) $value);
                    break;
                case 'order.datedelivery':
                    !$private ?: $item->set_date_delivery($value);
                    break;
                case 'order.datepayment':
                    !$private ?: $item->set_date_payment($value);
                    break;
                case 'order.customerid':
                    !$private ?: $item->set_customer_id($value);
                    break;
                case 'order.customerref':
                    $item->set_customer_reference($value);
                    break;
                case 'order.languageid':
                    $item->locale()->set_language_id($value);
                    break;
                case 'order.relatedid':
                    $item->set_related_id($value);
                    break;
                case 'order.comment':
                    $item->set_comment($value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns the item values as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $list = parent::to_array($private);
        $list['order.channel'] = $this->get_channel();
        $list['order.invoiceno'] = $this->get_invoice_number();
        $list['order.statusdelivery'] = $this->get_status_delivery();
        $list['order.statuspayment'] = $this->get_status_payment();
        $list['order.datedelivery'] = $this->get_date_delivery();
        $list['order.datepayment'] = $this->get_date_payment();
        $list['order.relatedid'] = $this->get_related_id();
        $list['order.sitecode'] = $this->get_site_code();
        $list['order.customerid'] = $this->get_customer_id();
        $list['order.languageid'] = $this->locale()->get_language_id();
        $list['order.customerref'] = $this->get_customer_reference();
        $list['order.comment'] = $this->get_comment();
        return $list;
    }
}