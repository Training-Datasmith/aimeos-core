<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Item\Code;

/**
 * Default coupon code implementation.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Coupon\Item\Code\Iface
{
    /**
     * Returns the unique ID of the coupon item the code belongs to.
     *
     * @return string|null Unique ID of the coupon item
     */
    public function get_parent_id(): ?string
    {
        return $this->get('coupon.code.parentid');
    }
    /**
     * Sets the new unique ID of the coupon item the code belongs to.
     *
     * @param string|null $id Unique ID of the coupon item
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_parent_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('coupon.code.parentid', $id);
    }
    /**
     * Returns the code of the coupon item.
     *
     * @return string|null Coupon code
     */
    public function get_code(): ?string
    {
        return $this->get('coupon.code.code');
    }
    /**
     * Sets the new code for the coupon item.
     *
     * @param string $code Coupon code
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Coupon\Item\Code\Iface
    {
        return $this->set('coupon.code.code', \Aimeos\Utils::code($code));
    }
    /**
     * Returns the number of tries the code is valid.
     *
     * @return int|null Number of available tries or null for unlimited
     */
    public function get_count(): ?int
    {
        if (($result = $this->get('coupon.code.count', 0)) !== null) {
            return $result;
        }
        return null;
    }
    /**
     * Sets the new number of tries the code is valid.
     *
     * @param int|null $count Number of tries or null for unlimited
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_count($count = null): \Aimeos\M_Shop\Coupon\Item\Code\Iface
    {
        return $this->set('coupon.code.count', is_numeric($count) ? (int) $count : null);
    }
    /**
     * Returns the starting point of time, in which the code is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_start(): ?string
    {
        $value = $this->get('coupon.code.datestart');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new starting point of time, in which the code is available.
     *
     * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_date_start(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('coupon.code.datestart', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the ending point of time, in which the code is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_end(): ?string
    {
        $value = $this->get('coupon.code.dateend');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new ending point of time, in which the code is available.
     *
     * @param string|null New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_date_end(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('coupon.code.dateend', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns reference for the coupon code
     * This can be an arbitrary value used by the coupon provider
     *
     * @return string Arbitrary value depending on the coupon provider
     */
    public function get_ref(): string
    {
        return $this->get('coupon.code.ref', '');
    }
    /**
     * Sets the new reference for the coupon code
     * This can be an arbitrary value used by the coupon provider
     *
     * @param string|null $ref Arbitrary value depending on the coupon provider
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_ref(?string $ref): \Aimeos\M_Shop\Coupon\Item\Code\Iface
    {
        return $this->set('coupon.code.ref', (string) $ref);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        $date = $this->get('.date');
        return parent::is_available() && ($this->get_date_start() === null || $this->get_date_start() < $date) && ($this->get_date_end() === null || $this->get_date_end() > $date);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'coupon.code.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'coupon.code.datestart':
                    $item->set_date_start($value);
                    break;
                case 'coupon.code.dateend':
                    $item->set_date_end($value);
                    break;
                case 'coupon.code.count':
                    $item->set_count($value);
                    break;
                case 'coupon.code.code':
                    $item->set_code($value);
                    break;
                case 'coupon.code.ref':
                    $item->set_ref($value);
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
        $list['coupon.code.datestart'] = $this->get_date_start();
        $list['coupon.code.dateend'] = $this->get_date_end();
        $list['coupon.code.count'] = $this->get_count();
        $list['coupon.code.code'] = $this->get_code();
        $list['coupon.code.ref'] = $this->get_ref();
        if ($private === true) {
            $list['coupon.code.parentid'] = $this->get_parent_id();
        }
        return $list;
    }
}