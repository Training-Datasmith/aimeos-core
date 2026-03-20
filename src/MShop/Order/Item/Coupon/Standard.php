<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Coupon;

/**
 * Default implementation for order item base coupon.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Order\Item\Coupon\Iface
{
    /**
     * Returns the base ID of the order.
     *
     * @return string|null Order base ID
     */
    public function get_parent_id(): ?string
    {
        return $this->get('order.coupon.parentid');
    }
    /**
     * Sets the Base ID of the order.
     *
     * @param string|null $parentid Order base ID.
     * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order base coupon item for chaining method calls
     */
    public function set_parent_id(?string $parentid): \Aimeos\M_Shop\Order\Item\Coupon\Iface
    {
        return $this->set('order.coupon.parentid', (string) $parentid);
    }
    /**
     * Returns the ID of the ordered product.
     *
     * @return string|null ID of the ordered product.
     */
    public function get_product_id(): ?string
    {
        return $this->get('order.coupon.productid');
    }
    /**
     * Sets the ID of the ordered product.
     *
     * @param string $productid ID of the ordered product
     * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order base coupon item for chaining method calls
     */
    public function set_product_id(string $productid): \Aimeos\M_Shop\Order\Item\Coupon\Iface
    {
        return $this->set('order.coupon.productid', $productid);
    }
    /**
     * Returns the coupon code.
     *
     * @return string|null Coupon code.
     */
    public function get_code(): ?string
    {
        return $this->get('order.coupon.code');
    }
    /**
     * Sets the coupon code.
     *
     * @param string $code Coupon code
     * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order base coupon item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Order\Item\Coupon\Iface
    {
        return $this->set('order.coupon.code', \Aimeos\Utils::code($code));
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order coupon item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.coupon.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'order.coupon.productid':
                    !$private ?: $item->set_product_id($value);
                    break;
                case 'order.coupon.code':
                    $item->set_code($value);
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
        $list['order.coupon.code'] = $this->get_code();
        if ($private === true) {
            $list['order.coupon.parentid'] = $this->get_parent_id();
            $list['order.coupon.productid'] = $this->get_product_id();
        }
        return $list;
    }
}