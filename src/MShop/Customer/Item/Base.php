<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Customer
 */
namespace Aimeos\M_Shop\Customer\Item;

use Aimeos\M_Shop\Common\Item\Address_Ref;
use Aimeos\M_Shop\Common\Item\Lists_Ref;
use Aimeos\M_Shop\Common\Item\Property_Ref;
/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
abstract class Base extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Customer\Item\Iface
{
    use Lists_Ref\Traits, Property_Ref\Traits, Address_Ref\Traits {
        Lists_Ref\Traits::__clone as __cloneList;
        Address_Ref\Traits::__clone as __cloneAddress;
        Property_Ref\Traits::__clone as __cloneProperty;
    }
    private \Aimeos\M_Shop\Common\Item\Address\Iface $payaddress;
    /**
     * Initializes the customer item object
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
     * @param string $prefix Property prefix for the values
     * @param array $values List of attributes that belong to the customer item
     */
    public function __construct(\Aimeos\M_Shop\Common\Item\Address\Iface $address, string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values);
        $this->init_list_items($values['.listitems'] ?? []);
        $this->init_address_items($values['.addritems'] ?? []);
        $this->init_property_items($values['.propitems'] ?? []);
        $this->payaddress = $address->set_id($this->get_id());
        // set modified flag to false
    }
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        $this->payaddress = clone $this->payaddress;
        parent::__clone();
        $this->__clone_list();
        $this->__clone_address();
        $this->__clone_property();
    }
    /**
     * Returns the payaddress of the customer item.
     */
    public function get_payment_address(): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->payaddress;
    }
    /**
     * Sets the payaddress of the customer item.
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billingaddress of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_payment_address(\Aimeos\M_Shop\Common\Item\Address\Iface $address): \Aimeos\M_Shop\Customer\Item\Iface
    {
        if ($address === $this->payaddress && $address->is_modified() === false) {
            return $this;
        }
        $this->payaddress = $address;
        $this->set_modified();
        return $this;
    }
    /**
     * Tests if this item object was modified
     *
     * @return bool True if modified, false if not
     */
    public function is_modified(): bool
    {
        if (parent::is_modified()) {
            return true;
        }
        return $this->get_payment_address()->is_modified();
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        $addr = $item->get_payment_address()->from_array($list, $private);
        return $item->set_payment_address($addr);
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
        $list['customer.salutation'] = $this->get_payment_address()->get_salutation();
        $list['customer.company'] = $this->get_payment_address()->get_company();
        $list['customer.vatid'] = $this->get_payment_address()->get_vat_id();
        $list['customer.title'] = $this->get_payment_address()->get_title();
        $list['customer.firstname'] = $this->get_payment_address()->get_firstname();
        $list['customer.lastname'] = $this->get_payment_address()->get_lastname();
        $list['customer.address1'] = $this->get_payment_address()->get_address1();
        $list['customer.address2'] = $this->get_payment_address()->get_address2();
        $list['customer.address3'] = $this->get_payment_address()->get_address3();
        $list['customer.postal'] = $this->get_payment_address()->get_postal();
        $list['customer.city'] = $this->get_payment_address()->get_city();
        $list['customer.state'] = $this->get_payment_address()->get_state();
        $list['customer.languageid'] = $this->get_payment_address()->get_language_id();
        $list['customer.countryid'] = $this->get_payment_address()->get_country_id();
        $list['customer.telephone'] = $this->get_payment_address()->get_telephone();
        $list['customer.mobile'] = $this->get_payment_address()->get_mobile();
        $list['customer.email'] = $this->get_payment_address()->get_email();
        $list['customer.telefax'] = $this->get_payment_address()->get_telefax();
        $list['customer.website'] = $this->get_payment_address()->get_website();
        $list['customer.longitude'] = $this->get_payment_address()->get_longitude();
        $list['customer.latitude'] = $this->get_payment_address()->get_latitude();
        $list['customer.birthday'] = $this->get_payment_address()->get_birthday();
        return $list;
    }
}