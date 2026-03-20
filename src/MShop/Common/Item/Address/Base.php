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
 * Abstract class for address items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Common\Item\Address\Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    private string $prefix;
    /**
     * Initializes the address item.
     *
     * @param string $prefix Key prefix that should be used for toArray()/fromArray() like "customer.address."
     * @param array $values Associative list of key/value pairs containing address data
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values, str_replace('.', '/', rtrim($prefix, '.')));
        $this->prefix = $prefix;
    }
    /**
     * Returns the company name.
     *
     * @return string Company name
     */
    public function get_company(): string
    {
        return (string) $this->get($this->prefix . 'company', '');
    }
    /**
     * Sets a new company name.
     *
     * @param string|null $company New company name
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_company(?string $company): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'company', (string) $company);
    }
    /**
     * Returns the vatid.
     *
     * @return string vatid
     */
    public function get_vat_id(): string
    {
        return (string) $this->get($this->prefix . 'vatid', '');
    }
    /**
     * Sets a new vatid.
     *
     * @param string|null $vatid New vatid
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_vat_id(?string $vatid): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'vatid', str_replace(' ', '', (string) $vatid));
    }
    /**
     * Returns the salutation constant for the person described by the address.
     *
     * @return string Saluatation code
     */
    public function get_salutation(): string
    {
        return $this->get($this->prefix . 'salutation', '');
    }
    /**
     * Sets the new salutation for the person described by the address.
     *
     * @param string|null $salutation Salutation constant defined in \Aimeos\MShop\Common\Item\Address\Base
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_salutation(?string $salutation): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'salutation', $this->check_salutation((string) $salutation));
    }
    /**
     * Returns the title of the person.
     *
     * @return string Title of the person
     */
    public function get_title(): string
    {
        return $this->get($this->prefix . 'title', '');
    }
    /**
     * Sets a new title of the person.
     *
     * @param string|null $title New title of the person
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_title(?string $title): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'title', (string) $title);
    }
    /**
     * Returns the first name of the person.
     *
     * @return string First name of the person
     */
    public function get_firstname(): string
    {
        return $this->get($this->prefix . 'firstname', '');
    }
    /**
     * Sets a new first name of the person.
     *
     * @param string|null $firstname New first name of the person
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_firstname(?string $firstname): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'firstname', (string) $firstname);
    }
    /**
     * Returns the last name of the person.
     *
     * @return string Last name of the person
     */
    public function get_lastname(): string
    {
        return $this->get($this->prefix . 'lastname', '');
    }
    /**
     * Sets a new last name of the person.
     *
     * @param string|null $lastname New last name of the person
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_lastname(?string $lastname): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'lastname', (string) $lastname);
    }
    /**
     * Returns the first address part, e.g. the street name.
     *
     * @return string First address part
     */
    public function get_address1(): string
    {
        return $this->get($this->prefix . 'address1', '');
    }
    /**
     * Sets a new first address part, e.g. the street name.
     *
     * @param string|null $address1 New first address part
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_address1(?string $address1): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'address1', (string) $address1);
    }
    /**
     * Returns the second address part, e.g. the house number.
     *
     * @return string Second address part
     */
    public function get_address2(): string
    {
        return $this->get($this->prefix . 'address2', '');
    }
    /**
     * Sets a new second address part, e.g. the house number.
     *
     * @param string|null $address2 New second address part
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_address2(?string $address2): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'address2', (string) $address2);
    }
    /**
     * Returns the third address part, e.g. the house name or floor number.
     *
     * @return string third address part
     */
    public function get_address3(): string
    {
        return $this->get($this->prefix . 'address3', '');
    }
    /**
     * Sets a new third address part, e.g. the house name or floor number.
     *
     * @param string|null $address3 New third address part
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_address3(?string $address3): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'address3', (string) $address3);
    }
    /**
     * Returns the postal code.
     *
     * @return string Postal code
     */
    public function get_postal(): string
    {
        return $this->get($this->prefix . 'postal', '');
    }
    /**
     * Sets a new postal code.
     *
     * @param string|null $postal New postal code
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_postal(?string $postal): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'postal', (string) $postal);
    }
    /**
     * Returns the city name.
     *
     * @return string City name
     */
    public function get_city(): string
    {
        return $this->get($this->prefix . 'city', '');
    }
    /**
     * Sets a new city name.
     *
     * @param string|null $city New city name
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_city(?string $city): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'city', (string) $city);
    }
    /**
     * Returns the state name.
     *
     * @return string State name
     */
    public function get_state(): string
    {
        return $this->get($this->prefix . 'state', '');
    }
    /**
     * Sets a new state name.
     *
     * @param string|null $state New state name
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_state(?string $state): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'state', (string) $state);
    }
    /**
     * Returns the unique ID of the country the address belongs to.
     *
     * @return string|null Unique ID of the country
     */
    public function get_country_id(): ?string
    {
        return $this->get($this->prefix . 'countryid');
    }
    /**
     * Sets the ID of the country the address is in.
     *
     * @param string|null $countryid Unique ID of the country
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_country_id(?string $countryid): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'countryid', \Aimeos\Utils::country($countryid));
    }
    /**
     * Returns the unique ID of the language.
     *
     * @return string|null Unique ID of the language
     */
    public function get_language_id(): ?string
    {
        return $this->get($this->prefix . 'languageid');
    }
    /**
     * Sets the ID of the language.
     *
     * @param string|null $langid Unique ID of the language
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_language_id(?string $langid): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'languageid', \Aimeos\Utils::language($langid));
    }
    /**
     * Returns the telephone number.
     *
     * @return string Telephone number
     */
    public function get_telephone(): string
    {
        return $this->get($this->prefix . 'telephone', '');
    }
    /**
     * Sets a new telephone number.
     *
     * @param string|null $telephone New telephone number
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_telephone(?string $telephone): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'telephone', (string) $telephone);
    }
    /**
     * Returns the telefax number.
     *
     * @return string Telefax number
     */
    public function get_telefax(): string
    {
        return $this->get($this->prefix . 'telefax', '');
    }
    /**
     * Sets a new telefax number.
     *
     * @param string|null $telefax New telefax number
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_telefax(?string $telefax): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'telefax', (string) $telefax);
    }
    /**
     * Returns the mobile number.
     *
     * @return string Mobile number
     */
    public function get_mobile(): string
    {
        return $this->get($this->prefix . 'mobile', '');
    }
    /**
     * Sets a new mobile number.
     *
     * @param string|null $value New mobile number
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_mobile(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'mobile', (string) $value);
    }
    /**
     * Returns the email address.
     *
     * @return string Email address
     */
    public function get_email(): string
    {
        return $this->get($this->prefix . 'email', '');
    }
    /**
     * Sets a new email address.
     *
     * @param string|null $email New email address
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_email(?string $email): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        $email = (string) $email;
        $regex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        if ($email != '' && preg_match($regex, $email) !== 1) {
            throw new \Aimeos\M_Shop\Exception(sprintf('Invalid characters in email address: "%1$s"', $email));
        }
        return $this->set($this->prefix . 'email', $email);
    }
    /**
     * Returns the website URL.
     *
     * @return string Website URL
     */
    public function get_website(): string
    {
        return $this->get($this->prefix . 'website', '');
    }
    /**
     * Sets a new website URL.
     *
     * @param string|null $website New website URL
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_website(?string $website): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        $website = (string) $website;
        $pattern = '#^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$#';
        if ($website != '' && preg_match($pattern, $website) !== 1) {
            throw new \Aimeos\M_Shop\Exception(sprintf('Invalid web site URL "%1$s"', $website));
        }
        return $this->set($this->prefix . 'website', $website);
    }
    /**
     * Returns the longitude coordinate of the customer address
     *
     * @return float|null Longitude coordinate as decimal value or null
     */
    public function get_longitude(): ?float
    {
        if (($result = $this->get($this->prefix . 'longitude')) !== null) {
            return (float) $result;
        }
        return null;
    }
    /**
     * Sets the longitude coordinate of the customer address
     *
     * @param string|null $value Longitude coordinate as decimal value or null
     * @return \Aimeos\MShop\Customer\Item\Iface Customer address item for chaining method calls
     */
    public function set_longitude(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'longitude', $value !== '' && $value !== null ? $value : null);
    }
    /**
     * Returns the latitude coordinate of the customer address
     *
     * @return float|null Latitude coordinate as decimal value or null
     */
    public function get_latitude(): ?float
    {
        if (($result = $this->get($this->prefix . 'latitude')) !== null) {
            return (float) $result;
        }
        return null;
    }
    /**
     * Sets the latitude coordinate of the customer address
     *
     * @param string|null $value Latitude coordinate as decimal value or null
     * @return \Aimeos\MShop\Customer\Item\Iface Customer address item for chaining method calls
     */
    public function set_latitude(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'latitude', $value !== '' && $value !== null ? $value : null);
    }
    /**
     * Returns the birthday of the customer item.
     *
     * @return string|null Birthday in YYYY-MM-DD format
     */
    public function get_birthday(): ?string
    {
        return $this->get($this->prefix . 'birthday');
    }
    /**
     * Sets the birthday of the customer item.
     *
     * @param string|null $value Birthday of the customer item
     * @return \Aimeos\MShop\Common\Item\Address\Iface Customer address item for chaining method calls
     */
    public function set_birthday(?string $value): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        return $this->set($this->prefix . 'birthday', \Aimeos\Utils::date($value));
    }
    /**
     * Returns the customer ID this address belongs to
     *
     * @return string|null Customer ID of the address
     */
    public function get_parent_id(): ?string
    {
        return $this->get($this->prefix . 'parentid');
    }
    /**
     * Sets the new customer ID this address belongs to
     *
     * @param string|null $parentid New customer ID of the address
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_parent_id(?string $parentid): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set($this->prefix . 'parentid', $parentid);
    }
    /**
     * Returns the position of the address item.
     *
     * @return int Position of the address item
     */
    public function get_position(): int
    {
        return $this->get($this->prefix . 'position', 0);
    }
    /**
     * Sets the Position of the address item.
     *
     * @param int $position Position of the address item
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function set_position(int $position): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set($this->prefix . 'position', $position);
    }
    /**
     * Returns the type of the address item.
     * Overwritten for different default value.
     *
     * @return string Address type
     */
    public function get_type(): string
    {
        return $this->get($this->prefix . 'type', 'delivery');
    }
    /**
     * Copies the values of the address item into another one.
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $item Address item
     * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Common\Item\Address\Iface $item): \Aimeos\M_Shop\Common\Item\Address\Iface
    {
        $values = $item->to_array();
        $this->from_array($values);
        $this->set_type($item->get_type());
        $this->set_company($item->get_company());
        $this->set_vat_id($item->get_vat_id());
        $this->set_salutation($item->get_salutation());
        $this->set_title($item->get_title());
        $this->set_firstname($item->get_firstname());
        $this->set_lastname($item->get_lastname());
        $this->set_address1($item->get_address1());
        $this->set_address2($item->get_address2());
        $this->set_address3($item->get_address3());
        $this->set_postal($item->get_postal());
        $this->set_city($item->get_city());
        $this->set_state($item->get_state());
        $this->set_country_id($item->get_country_id());
        $this->set_language_id($item->get_language_id());
        $this->set_telephone($item->get_telephone());
        $this->set_telefax($item->get_telefax());
        $this->set_mobile($item->get_mobile());
        $this->set_email($item->get_email());
        $this->set_website($item->get_website());
        $this->set_longitude($item->get_longitude());
        $this->set_latitude($item->get_latitude());
        $this->set_birthday($item->get_birthday());
        return $this;
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Common\Item\Address\Iface Address item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $idx => $value) {
            $pos = strrpos($idx, '.');
            $key = $pos ? substr($idx, $pos + 1) : $idx;
            switch ($key) {
                case 'parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'type':
                    $item->set_type($value);
                    break;
                case 'salutation':
                    $item->set_salutation($value);
                    break;
                case 'company':
                    $item->set_company($value);
                    break;
                case 'vatid':
                    $item->set_vat_id($value);
                    break;
                case 'title':
                    $item->set_title($value);
                    break;
                case 'firstname':
                    $item->set_firstname($value);
                    break;
                case 'lastname':
                    $item->set_lastname($value);
                    break;
                case 'address1':
                    $item->set_address1($value);
                    break;
                case 'address2':
                    $item->set_address2($value);
                    break;
                case 'address3':
                    $item->set_address3($value);
                    break;
                case 'postal':
                    $item->set_postal($value);
                    break;
                case 'city':
                    $item->set_city($value);
                    break;
                case 'state':
                    $item->set_state($value);
                    break;
                case 'countryid':
                    $item->set_country_id($value);
                    break;
                case 'languageid':
                    $item->set_language_id($value);
                    break;
                case 'telephone':
                    $item->set_telephone($value);
                    break;
                case 'telefax':
                    $item->set_telefax($value);
                    break;
                case 'mobile':
                    $item->set_mobile($value);
                    break;
                case 'email':
                    $item->set_email($value);
                    break;
                case 'website':
                    $item->set_website($value);
                    break;
                case 'longitude':
                    $item->set_longitude($value);
                    break;
                case 'latitude':
                    $item->set_latitude($value);
                    break;
                case 'birthday':
                    $item->set_birthday($value);
                    break;
                case 'position':
                    $item->set_position($value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$idx]);
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
        $list[$this->prefix . 'type'] = $this->get_type();
        $list[$this->prefix . 'salutation'] = $this->get_salutation();
        $list[$this->prefix . 'company'] = $this->get_company();
        $list[$this->prefix . 'vatid'] = $this->get_vat_id();
        $list[$this->prefix . 'title'] = $this->get_title();
        $list[$this->prefix . 'firstname'] = $this->get_firstname();
        $list[$this->prefix . 'lastname'] = $this->get_lastname();
        $list[$this->prefix . 'address1'] = $this->get_address1();
        $list[$this->prefix . 'address2'] = $this->get_address2();
        $list[$this->prefix . 'address3'] = $this->get_address3();
        $list[$this->prefix . 'postal'] = $this->get_postal();
        $list[$this->prefix . 'city'] = $this->get_city();
        $list[$this->prefix . 'state'] = $this->get_state();
        $list[$this->prefix . 'countryid'] = $this->get_country_id();
        $list[$this->prefix . 'languageid'] = $this->get_language_id();
        $list[$this->prefix . 'telephone'] = $this->get_telephone();
        $list[$this->prefix . 'telefax'] = $this->get_telefax();
        $list[$this->prefix . 'mobile'] = $this->get_mobile();
        $list[$this->prefix . 'email'] = $this->get_email();
        $list[$this->prefix . 'website'] = $this->get_website();
        $list[$this->prefix . 'longitude'] = $this->get_longitude();
        $list[$this->prefix . 'latitude'] = $this->get_latitude();
        $list[$this->prefix . 'birthday'] = $this->get_birthday();
        $list[$this->prefix . 'position'] = $this->get_position();
        if ($private === true) {
            $list[$this->prefix . 'parentid'] = $this->get_parent_id();
        }
        return $list;
    }
    /**
     * Checks the given address salutation is valid
     *
     * @param string $value Address salutation defined in \Aimeos\MShop\Common\Item\Address\Base
     * @throws \Aimeos\MShop\Exception If salutation is invalid
     */
    protected function check_salutation(string $value)
    {
        if (strlen($value) > 8) {
            throw new \Aimeos\M_Shop\Exception(sprintf('Address salutation "%1$s" not within allowed range', $value));
        }
        return $value;
    }
    /**
     * Returns the prefix for toArray() and fromArray() methods.
     *
     * @return string Prefix for toArray() and fromArray() methods
     */
    protected function prefix(): string
    {
        return $this->prefix;
    }
}