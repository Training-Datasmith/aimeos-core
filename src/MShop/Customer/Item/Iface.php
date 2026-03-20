<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Customer
 */
namespace Aimeos\M_Shop\Customer\Item;

/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Address_Ref\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Property_Ref\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the label of the customer item.
     *
     * @return string Label of the customer item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the customer item.
     *
     * @param string $value Label of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_label(?string $value): \Aimeos\M_Shop\Customer\Item\Iface;
    /**
     * Returns the unique code of the customer item.
     * This should be the username or the e-mail address.
     *
     * @return string Unique code of the customer item
     */
    public function get_code(): string;
    /**
     * Sets the code of the customer item.
     *
     * @param string $value Code of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_code(string $value): \Aimeos\M_Shop\Customer\Item\Iface;
    /**
     * Returns the billing address of the customer item.
     *
     * @return \Aimeos\MShop\Common\Item\Address\Iface Address object
     */
    public function get_payment_address(): \Aimeos\M_Shop\Common\Item\Address\Iface;
    /**
     * Sets the billing address of the customer item.
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billing address of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_payment_address(\Aimeos\M_Shop\Common\Item\Address\Iface $address): \Aimeos\M_Shop\Customer\Item\Iface;
    /**
     * Returns the password of the customer item.
     *
     * @return string Encrypted password
     */
    public function get_password(): string;
    /**
     * Sets the password of the customer item.
     *
     * @param string $value Password of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_password(string $value): \Aimeos\M_Shop\Customer\Item\Iface;
    /**
     * Returns the last verification date of the customer.
     *
     * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
     */
    public function get_date_verified(): ?string;
    /**
     * Sets the latest verification date of the customer.
     *
     * @param string|null $value Latest verification date of the customer (YYYY-MM-DD format) or null if unknown
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_date_verified(?string $value): \Aimeos\M_Shop\Customer\Item\Iface;
    /**
     * Returns the group IDs the customer belongs to
     *
     * @return array List of group IDs
     */
    public function get_groups(): array;
    /**
     * Sets the group IDs the customer belongs to
     *
     * @param string[] $ids List of group IDs
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_groups(array $ids): \Aimeos\M_Shop\Customer\Item\Iface;
    /**
     * Tests if the user is a super user
     *
     * @return bool TRUE if user is a super user, FALSE if not
     */
    public function is_super(): bool;
}