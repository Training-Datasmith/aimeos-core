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
class Standard extends Base implements Iface
{
    private ?array $groups = null;
    /**
     * Initializes the customer item object
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
     * @param string $prefix Prefix for the keys returned by toArray()
     * @param array $values List of attributes that belong to the customer item
     * @param \Aimeos\Base\Password\Iface|null $passwd Password encryption object
     */
    public function __construct(\Aimeos\M_Shop\Common\Item\Address\Iface $address, string $prefix, array $values = [], private ?\Aimeos\Base\Password\Iface $passwd = null)
    {
        parent::__construct($address, $prefix, $values);
    }
    /**
     * Sets the new ID of the item.
     *
     * @param string|null $id ID of the item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        parent::set_id($id);
        // set new ID and modified flag
        $this->get_payment_address()->set_id(null)->set_id($this->get_id());
        return $this;
    }
    /**
     * Returns the label of the customer item.
     *
     * @return string Label of the customer item
     */
    public function get_label(): string
    {
        return $this->get('customer.label', '');
    }
    /**
     * Sets the new label of the customer item.
     *
     * @param string $value Label of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_label(?string $value): \Aimeos\M_Shop\Customer\Item\Iface
    {
        return $this->set('customer.label', (string) $value);
    }
    /**
     * Returns the status of the item.
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('customer.status', 1);
    }
    /**
     * Sets the status of the item.
     *
     * @param int $value Status of the item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_status(int $value): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('customer.status', $value);
    }
    /**
     * Returns the code of the customer item.
     *
     * @return string Code of the customer item
     */
    public function get_code(): string
    {
        return (string) $this->get('customer.code', $this->get_payment_address()->get_email());
    }
    /**
     * Sets the new code of the customer item.
     *
     * @param string $value Code of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_code(string $value): \Aimeos\M_Shop\Customer\Item\Iface
    {
        if ($value !== $this->get('customer.code')) {
            $this->set_date_verified(null);
        }
        return $this->set('customer.code', \Aimeos\Utils::code($value, 255));
    }
    /**
     * Returns the password of the customer item.
     */
    public function get_password(): string
    {
        return (string) $this->get('customer.password', '');
    }
    /**
     * Sets the password of the customer item.
     *
     * @param string $value password of the customer item
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_password(string $value): \Aimeos\M_Shop\Customer\Item\Iface
    {
        if ($this->passwd && $value !== $this->get_password()) {
            $value = $this->passwd->hash($value);
        }
        return $this->set('customer.password', $value);
    }
    /**
     * Returns the last verification date of the customer.
     *
     * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
     */
    public function get_date_verified(): ?string
    {
        return $this->get('customer.dateverified');
    }
    /**
     * Sets the latest verification date of the customer.
     *
     * @param string|null $value Latest verification date of the customer (YYYY-MM-DD) or null if unknown
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_date_verified(?string $value): \Aimeos\M_Shop\Customer\Item\Iface
    {
        return $this->set('customer.dateverified', \Aimeos\Utils::date($value));
    }
    /**
     * Returns the group IDs the customer belongs to
     *
     * @return array List of group IDs
     */
    public function get_groups(): array
    {
        if (!isset($this->groups)) {
            if (($list = (array) $this->get('customer.groups', [])) === []) {
                $list = $this->get_ref_items('group', null, 'default')->col('group.id')->all();
            }
            $this->groups = $list;
        }
        return $this->groups;
    }
    /**
     * Sets the group IDs/codes the customer belongs to
     *
     * @param array $value List of group IDs
     * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
     */
    public function set_groups(array $value): \Aimeos\M_Shop\Customer\Item\Iface
    {
        $list = $this->get_groups();
        if (array_diff($value, $list) !== [] || array_diff($list, $value) !== []) {
            $this->groups = $value;
            $this->set_modified();
        }
        return $this;
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
     * Tests if the user is a super user
     *
     * @return bool TRUE if user is a super user, FALSE if not
     */
    public function is_super(): bool
    {
        return (bool) $this->get('.super', false);
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
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'customer.label':
                    $item->set_label($value);
                    break;
                case 'customer.code':
                    !$private ?: $item->set_code($value);
                    break;
                case 'customer.status':
                    !$private ?: $item->set_status((int) $value);
                    break;
                case 'customer.groups':
                    !$private ?: $item->set_groups((array) $value);
                    break;
                case 'customer.password':
                    !$private ?: $item->set_password($value);
                    break;
                case 'customer.dateverified':
                    !$private ?: $item->set_date_verified($value);
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
        $list['customer.label'] = $this->get_label();
        $list['customer.code'] = $this->get_code();
        if ($private === true) {
            $list['customer.status'] = $this->get_status();
            $list['customer.groups'] = $this->get_groups();
            $list['customer.password'] = $this->get_password();
            $list['customer.dateverified'] = $this->get_date_verified();
        }
        return $list;
    }
}