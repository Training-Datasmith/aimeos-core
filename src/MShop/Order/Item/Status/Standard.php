<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Status;

/**
 * Default implementation of the order status object.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Order\Item\Status\Base implements \Aimeos\M_Shop\Order\Item\Status\Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Returns the parentid of the order status.
     *
     * @return string|null Parent ID of the order
     */
    public function get_parent_id(): ?string
    {
        return $this->get('order.status.parentid');
    }
    /**
     * Sets the parentid of the order status.
     *
     * @param string|null $parentid Parent ID of the order status
     * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
     */
    public function set_parent_id(?string $parentid): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('order.status.parentid', $parentid);
    }
    /**
     * Returns the value of the order status.
     *
     * @return string Value of the order status
     */
    public function get_value(): string
    {
        return (string) $this->get('order.status.value', '');
    }
    /**
     * Sets the value of the order status.
     *
     * @param string $value Value of the order status
     * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
     */
    public function set_value(string $value): \Aimeos\M_Shop\Order\Item\Status\Iface
    {
        return $this->set('order.status.value', $value);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.status.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'order.status.type':
                    $item->set_type($value);
                    break;
                case 'order.status.value':
                    $item->set_value($value);
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
        $list['order.status.type'] = $this->get_type();
        $list['order.status.value'] = $this->get_value();
        if ($private === true) {
            $list['order.status.parentid'] = $this->get_parent_id();
        }
        return $list;
    }
}