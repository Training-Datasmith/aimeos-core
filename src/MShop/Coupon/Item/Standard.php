<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Item;

/**
 * Default coupon item implementation.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Coupon\Item\Iface
{
    use \Aimeos\M_Shop\Common\Item\Config\Traits;
    /**
     * Returns the label of the coupon item.
     *
     * @return string Name/label for this coupon
     */
    public function get_label(): string
    {
        return $this->get('coupon.label', '');
    }
    /**
     * Sets the label of the coupon item.
     *
     * @param string $name Coupon name, esp. short coupon class name
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function set_label(string $name): \Aimeos\M_Shop\Coupon\Item\Iface
    {
        return $this->set('coupon.label', $name);
    }
    /**
     * Returns the starting point of time, in which the coupon is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_start(): ?string
    {
        $value = $this->get('coupon.datestart');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new starting point of time, in which the coupon is available.
     *
     * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function set_date_start(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('coupon.datestart', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the ending point of time, in which the coupon is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_end(): ?string
    {
        $value = $this->get('coupon.dateend');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new ending point of time, in which the coupon is available.
     *
     * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function set_date_end(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('coupon.dateend', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the name of the provider class name to be used.
     *
     * @return string Returns the provider class name
     */
    public function get_provider(): string
    {
        return $this->get('coupon.provider', '');
    }
    /**
     * Set the name of the provider class name to be used.
     *
     * @param string $provider Provider class name
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function set_provider(string $provider): \Aimeos\M_Shop\Coupon\Item\Iface
    {
        return $this->set('coupon.provider', $provider);
    }
    /**
     * Returns the status of the coupon item.
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('coupon.status', 1);
    }
    /**
     * Sets the new status of the coupon item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('coupon.status', $status);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_status() > 0 && ($this->get_date_start() === null || $this->get_date_start() < $this->date) && ($this->get_date_end() === null || $this->get_date_end() > $this->date);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'coupon.label':
                    $item->set_label($value);
                    break;
                case 'coupon.datestart':
                    $item->set_date_start($value);
                    break;
                case 'coupon.dateend':
                    $item->set_date_end($value);
                    break;
                case 'coupon.provider':
                    $item->set_provider($value);
                    break;
                case 'coupon.status':
                    $item->set_status((int) $value);
                    break;
                case 'coupon.config':
                    $item->set_config((array) $value);
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
        $list['coupon.config'] = $this->get_config();
        $list['coupon.label'] = $this->get_label();
        $list['coupon.datestart'] = $this->get_date_start();
        $list['coupon.dateend'] = $this->get_date_end();
        $list['coupon.provider'] = $this->get_provider();
        $list['coupon.status'] = $this->get_status();
        return $list;
    }
}