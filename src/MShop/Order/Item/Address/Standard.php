<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Address;

/**
 * Default order address container object
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Order\Item\Address\Base implements \Aimeos\M_Shop\Order\Item\Address\Iface
{
    /**
     * Returns the original customer address ID.
     *
     * @return string Customer address ID
     */
    public function get_address_id(): string
    {
        return $this->get('order.address.addressid', '');
    }
    /**
     * Sets the original customer address ID.
     *
     * @param string $addrid New customer address ID
     * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
     */
    public function set_address_id(string $addrid): \Aimeos\M_Shop\Order\Item\Address\Iface
    {
        return $this->set('order.address.addressid', $addrid);
    }
    /**
     * Copys all data from a given address item.
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $item New address
     * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Common\Item\Address\Iface $item): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        if (self::macro('copyFrom')) {
            return $this->call('copyFrom', $item);
        }
        parent::copy_from($item);
        $this->set_address_id((string) $item->get_id());
        $this->set_modified();
        return $this;
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Address\Iface Order address item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.address.addressid':
                    $item->set_address_id($value);
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
        $list['order.address.addressid'] = $this->get_address_id();
        return $list;
    }
}