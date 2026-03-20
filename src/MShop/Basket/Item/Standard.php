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
 * Default implementation of the basket item
 *
 * @package MShop
 * @subpackage Basket
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Basket\Item\Iface
{
    /**
     * Initializes the object
     *
     * @param array $values Associative list of key/value pairs with basket properties
     * @param \Aimeos\MShop\Order\Item\Iface|null $item Basket object
     */
    public function __construct(array $values = [], private ?\Aimeos\M_Shop\Order\Item\Iface $item = null)
    {
        parent::__construct('basket.', $values);
    }
    /**
     * Sets the new ID of the item.
     *
     * @param string|null $id ID of the item
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        return parent::set_id($id)->set_modified();
    }
    /**
     * Returns the basket object.
     *
     * @return \Aimeos\MShop\Order\Item\Iface|null $basket Basket object
     */
    public function get_item(): ?\Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->item;
    }
    /**
     * Sets the basket object.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return \Aimeos\MShop\Basket\Item\Iface Basket item for chaining method calls
     */
    public function set_item(\Aimeos\M_Shop\Order\Item\Iface $basket): \Aimeos\M_Shop\Basket\Item\Iface
    {
        $this->item = $basket;
        return $this->set_modified();
    }
    /**
     * Returns the ID of the customer who owns the basket.
     *
     * @return string Unique ID of the customer
     */
    public function get_customer_id(): string
    {
        return (string) $this->get('basket.customerid', '');
    }
    /**
     * Sets the ID of the customer who owned the basket.
     *
     * @param string $customerid Unique ID of the customer
     * @return \Aimeos\MShop\Basket\Item\Iface Basket item for chaining method calls
     */
    public function set_customer_id(?string $value): \Aimeos\M_Shop\Basket\Item\Iface
    {
        return $this->set('basket.customerid', (string) $value);
    }
    /**
     * Returns the name of the basket.
     *
     * @return string Name for the basket
     */
    public function get_name(): string
    {
        return (string) $this->get('basket.name', '');
    }
    /**
     * Sets the name of the basket.
     *
     * @param string $value Name for the basket
     * @return \Aimeos\MShop\Basket\Item\Iface Basket item for chaining method calls
     */
    public function set_name(?string $value): \Aimeos\M_Shop\Basket\Item\Iface
    {
        return $this->set('basket.name', (string) $value);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Basket\Item\Iface Order status item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'basket.customerid':
                    $item->set_customer_id($value);
                    break;
                case 'basket.name':
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
        $list['basket.name'] = $this->get_name();
        $list['basket.customerid'] = $this->get_customer_id();
        return $list;
    }
}