<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Address;

/**
 * Interface for provider common address DTO objects used by the shop
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Parentid\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Copies the values of the address item into another one.
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $item Address item
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Common\Item\Address\Iface $item): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the company name.
     *
     * @return string Company name
     */
    public function get_company(): string;
    /**
     * Sets a new company name.
     *
     * @param string|null $value New company name
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_company(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the vatid.
     *
     * @return string vatid
     */
    public function get_vat_id(): string;
    /**
     * Sets a new vatid.
     *
     * @param string|null $value New vatid
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_vat_id(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the salutation constant for the person described by the address.
     *
     * @return string Saluatation constant defined in \Aimeos\MShop\Common\Item\Address\Base
     */
    public function get_salutation(): string;
    /**
     * Sets the new salutation for the person described by the address.
     *
     * @param string|null $value Salutation constant defined in \Aimeos\MShop\Common\Item\Address\Base
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_salutation(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the title of the person.
     *
     * @return string Title of the person
     */
    public function get_title(): string;
    /**
     * Sets a new title of the person.
     *
     * @param string|null $value New title of the person
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_title(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the first name of the person.
     *
     * @return string First name of the person
     */
    public function get_firstname(): string;
    /**
     * Sets a new first name of the person.
     *
     * @param string|null $value New first name of the person
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_firstname(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the last name of the perosn.
     *
     * @return string Last name of the person
     */
    public function get_lastname(): string;
    /**
     * Sets a new last name of the person.
     *
     * @param string|null $value New last name of the person
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_lastname(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the first address part, e.g. the street name.
     *
     * @return string First address part
     */
    public function get_address1(): string;
    /**
     * Sets a new first address part, e.g. the street name.
     *
     * @param string|null $value New first address part
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_address1(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the second address part, e.g. the house number.
     *
     * @return string Second address part
     */
    public function get_address2(): string;
    /**
     * Sets a new second address part, e.g. the house number.
     *
     * @param string|null $value New second address part
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_address2(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the third address part, e.g. the house name or floor number.
     *
     * @return string third address part
     */
    public function get_address3(): string;
    /**
     * Sets a new third address part, e.g. the house name or floor number.
     *
     * @param string|null $value New third address part
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_address3(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the postal code.
     *
     * @return string Postal code
     */
    public function get_postal(): string;
    /**
     * Sets a new postal code.
     *
     * @param string|null $value New postal code
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_postal(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the city name.
     *
     * @return string City name
     */
    public function get_city(): string;
    /**
     * Sets a new city name.
     *
     * @param string|null $value New city name
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_city(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the state name.
     *
     * @return string State name
     */
    public function get_state(): string;
    /**
     * Sets a new state name.
     *
     * @param string|null $value New state name
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_state(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the unique ID of the country the address belongs to.
     *
     * @return string|null Unique ID of the country
     */
    public function get_country_id(): ?string;
    /**
     * Sets the ID of the country the address is in.
     *
     * @param string|null $value Unique ID of the country
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_country_id(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the unique ID of the language.
     *
     * @return string|null Unique ID of the language
     */
    public function get_language_id(): ?string;
    /**
     * Sets the ID of the language.
     *
     * @param string $value Unique ID of the language
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_language_id(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the telephone number.
     *
     * @return string Telephone number
     */
    public function get_telephone(): string;
    /**
     * Sets a new telephone number.
     *
     * @param string|null $value New telephone number
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_telephone(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the telefax number.
     *
     * @return string Telefax number
     */
    public function get_telefax(): string;
    /**
     * Sets a new telefax number.
     *
     * @param string|null $value New telefax number
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_telefax(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the mobile number.
     *
     * @return string Mobile number
     */
    public function get_mobile(): string;
    /**
     * Sets a new mobile number.
     *
     * @param string|null $value New mobile number
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_mobile(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the email address.
     *
     * @return string Email address
     */
    public function get_email(): string;
    /**
     * Sets a new email address.
     *
     * @param string|null $value New email address
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_email(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the website URL.
     *
     * @return string Website URL
     */
    public function get_website(): string;
    /**
     * Sets a new website URL.
     *
     * @param string|null $value New website URL
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_website(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the longitude coordinate of the customer address
     *
     * @return float|null Longitude coordinate as decimal value or null
     */
    public function get_longitude(): ?float;
    /**
     * Sets the longitude coordinate of the customer address
     *
     * @param string|null $value Longitude coordinate as decimal value or null
     * @return \Aimeos\MShop\Common\Item\Iface Common address item for chaining method calls
     */
    public function set_longitude(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the latitude coordinate of the customer address
     *
     * @return float|null Latitude coordinate as decimal value or null
     */
    public function get_latitude(): ?float;
    /**
     * Sets the latitude coordinate of the customer address
     *
     * @param string|null $value Latitude coordinate as decimal value or null
     * @return \Aimeos\MShop\Common\Item\Iface Common address item for chaining method calls
     */
    public function set_latitude(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Returns the birthday of the customer item.
     *
     * @return string|null Birthday date of the customer (YYYY-MM-DD format)
     */
    public function get_birthday(): ?string;
    /**
     * Sets the birthday of the customer item.
     *
     * @param string|null $value Birthday of the customer item (YYYY-MM-DD format)
     * @return \Aimeos\MShop\Common\Item\Iface Common address item for chaining method calls
     */
    public function set_birthday(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface;
}