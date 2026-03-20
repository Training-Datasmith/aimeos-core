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
 * Default impelementation of a review item.
 *
 * @package MShop
 * @subpackage Review
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Review\Item\Iface
{
    /**
     * Returns the comment for the reviewed item
     *
     * @return string Comment for the reviewed item
     */
    public function get_comment(): string
    {
        return (string) $this->get('review.comment', '');
    }
    /**
     * Sets the new comment for the reviewed item
     *
     * @param string|null $value New comment for the reviewed item
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_comment(?string $value): \Aimeos\M_Shop\Review\Item\Iface
    {
        return $this->set('review.comment', strip_tags($value));
    }
    /**
     * Returns the ID of the reviewer
     *
     * @return string|null ID of the customer item
     */
    public function get_customer_id(): ?string
    {
        return (string) $this->get('review.customerid');
    }
    /**
     * Sets the ID of the reviewer
     *
     * @param string $value New ID of the customer item
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_customer_id(string $value): \Aimeos\M_Shop\Review\Item\Iface
    {
        return $this->set('review.customerid', $value);
    }
    /**
     * Returns the domain the review is valid for.
     *
     * @return string Domain name
     */
    public function get_domain(): string
    {
        return (string) $this->get('review.domain', '');
    }
    /**
     * Sets the new domain the review is valid for.
     *
     * @param string $value Domain name
     * @return \Aimeos\MShop\Common\Item\Iface Common item for chaining method calls
     */
    public function set_domain(string $value): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('review.domain', $value);
    }
    /**
     * Returns the ID of the ordered review
     *
     * @return string|null ID of the ordered review
     */
    public function get_order_product_id(): ?string
    {
        return (string) $this->get('review.orderproductid', '');
    }
    /**
     * Sets the ID of the ordered review item which the customer subscribed for
     *
     * @param string $value ID of the ordered review
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_order_product_id(string $value): \Aimeos\M_Shop\Review\Item\Iface
    {
        return $this->set('review.orderproductid', $value);
    }
    /**
     * Returns the name of the reviewer
     *
     * @return string Name of the reviewer
     */
    public function get_name(): string
    {
        return (string) $this->get('review.name', '');
    }
    /**
     * Sets the new name of the reviewer
     *
     * @param string $value New name of the reviewer
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_name(string $value): \Aimeos\M_Shop\Review\Item\Iface
    {
        return $this->set('review.name', strip_tags($value));
    }
    /**
     * Returns the rating for the reviewed item
     *
     * @return int Rating for the reviewed item (higher is better)
     */
    public function get_rating(): int
    {
        return (int) $this->get('review.rating', 0);
    }
    /**
     * Sets the new rating for the reviewed item
     *
     * @param int $value Rating for the reviewed item (higher is better)
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_rating(int $value): \Aimeos\M_Shop\Review\Item\Iface
    {
        return $this->set('review.rating', min(5, max(0, $value)));
    }
    /**
     * Returns the reference ID of the reviewed item, like the unique ID of a product item or a customer item
     *
     * @return string Reference ID of the common list item
     */
    public function get_ref_id(): string
    {
        return (string) $this->get('review.refid', '');
    }
    /**
     * Sets the new reference ID of the common list item, like the unique ID of a product item or a customer item
     *
     * @param string $value New reference ID of the common list item
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_ref_id(string $value): \Aimeos\M_Shop\Review\Item\Iface
    {
        return $this->set('review.refid', $value);
    }
    /**
     * Returns the response to the review
     *
     * @return string Response to the review
     */
    public function get_response(): string
    {
        return (string) $this->get('review.response', '');
    }
    /**
     * Sets the new response to the review
     *
     * @param string|null $value New response to the review
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_response(?string $value): \Aimeos\M_Shop\Review\Item\Iface
    {
        return $this->set('review.response', strip_tags($value));
    }
    /**
     * Returns the status of the review item.
     *
     * @return int Status of the review item
     */
    public function get_status(): int
    {
        return (int) $this->get('review.status', 1);
    }
    /**
     * Sets the new status of the review item.
     *
     * @param int $status New status of the review item
     * @return \Aimeos\MShop\Review\Item\Iface Review item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('review.status', $status);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_status() > 0;
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Common\Item\Iface Common item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'review.orderproductid':
                    !$private ?: $item->set_order_product_id($value);
                    break;
                case 'review.customerid':
                    !$private ?: $item->set_customer_id($value);
                    break;
                case 'review.refid':
                    $item->set_ref_id($value);
                    break;
                case 'review.domain':
                    $item->set_domain($value);
                    break;
                case 'review.comment':
                    $item->set_comment($value);
                    break;
                case 'review.response':
                    $item->set_response($value);
                    break;
                case 'review.status':
                    $item->set_status((int) $value);
                    break;
                case 'review.rating':
                    $item->set_rating((int) $value);
                    break;
                case 'review.name':
                    $item->set_name($value);
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
        $list['review.refid'] = $this->get_ref_id();
        $list['review.domain'] = $this->get_domain();
        $list['review.response'] = $this->get_response();
        $list['review.comment'] = $this->get_comment();
        $list['review.rating'] = $this->get_rating();
        $list['review.status'] = $this->get_status();
        $list['review.name'] = $this->get_name();
        $list['review.ctime'] = $this->get_time_created();
        if ($private) {
            $list['review.orderproductid'] = $this->get_order_product_id();
            $list['review.customerid'] = $this->get_customer_id();
        }
        return $list;
    }
}