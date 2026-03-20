<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Supplier
 */
namespace Aimeos\M_Shop\Supplier\Item;

use Aimeos\M_Shop\Common\Item\Address_Ref;
use Aimeos\M_Shop\Common\Item\Lists_Ref;
/**
 * Interface for supplier DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Supplier
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Supplier\Item\Iface
{
    use Lists_Ref\Traits, Address_Ref\Traits {
        Lists_Ref\Traits::__clone as __cloneList;
        Address_Ref\Traits::__clone as __cloneAddress;
    }
    /**
     * Initializes the supplier item object
     *
     * @param string $prefix Domain prefix
     * @param array $values List of attributes that belong to the supplier item
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values);
        $this->init_list_items($values['.listitems'] ?? []);
        $this->init_address_items($values['.addritems'] ?? []);
    }
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        $this->__clone_list();
        $this->__clone_address();
    }
    /**
     * Returns the label of the supplier item.
     *
     * @return string label of the supplier item
     */
    public function get_label(): string
    {
        return $this->get('supplier.label', '');
    }
    /**
     * Sets the new label of the supplier item.
     *
     * @param string $value label of the supplier item
     * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
     */
    public function set_label(string $value): \Aimeos\M_Shop\Supplier\Item\Iface
    {
        return $this->set('supplier.label', $value);
    }
    /**
     * Returns the code of the supplier item.
     *
     * @return string Code of the supplier item
     */
    public function get_code(): string
    {
        return $this->get('supplier.code', '');
    }
    /**
     * Sets the new code of the supplier item.
     *
     * @param string $value Code of the supplier item
     * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
     */
    public function set_code(string $value): \Aimeos\M_Shop\Supplier\Item\Iface
    {
        return $this->set('supplier.code', \Aimeos\Utils::code($value));
    }
    /**
     * Returns the position of the supplier item.
     *
     * @return int Position of the item
     */
    public function get_position(): int
    {
        return $this->get('supplier.position', 0);
    }
    /**
     * Sets the new position of the supplier item.
     *
     * @param int $position Position of the item
     * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
     */
    public function set_position(int $position): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('supplier.position', $position);
    }
    /**
     * Returns the status of the item
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('supplier.status', 1);
    }
    /**
     * Sets the new status of the supplier item.
     *
     * @param int $value status of the supplier item
     * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
     */
    public function set_status(int $value): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('supplier.status', $value);
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
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'supplier.code':
                    $item->set_code($value);
                    break;
                case 'supplier.label':
                    $item->set_label($value);
                    break;
                case 'supplier.status':
                    $item->set_status((int) $value);
                    break;
                case 'supplier.position':
                    $item->set_position((int) $value);
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
        $list['supplier.code'] = $this->get_code();
        $list['supplier.label'] = $this->get_label();
        $list['supplier.status'] = $this->get_status();
        $list['supplier.position'] = $this->get_position();
        return $list;
    }
}