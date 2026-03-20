<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MAdmin
 * @subpackage Cache
 */
namespace Aimeos\M_Admin\Cache\Item;

/**
 * Default cache item implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Admin\Cache\Item\Iface
{
    /**
     * Initializes the log item.
     *
     * @param array $values Associative list of key/value pairs
     */
    public function __construct(array $values = [])
    {
        parent::__construct('cache.', $values);
    }
    /**
     * Returns the ID of the item if available.
     *
     * @return string|null ID of the item
     */
    public function get_id(): ?string
    {
        return $this->get('id');
    }
    /**
     * Sets the unique ID of the item.
     *
     * @param string|null $id Unique ID of the item
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function set_id(?string $id = null): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('id', $id);
    }
    /**
     * Returns the value associated to the key.
     *
     * @return string Returns the value of the item
     */
    public function get_value(): string
    {
        return $this->get('value', '');
    }
    /**
     * Sets the new value of the item.
     *
     * @param string $value Value of the item or null for no expiration
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function set_value(string $value): \Aimeos\M_Admin\Cache\Item\Iface
    {
        return $this->set('value', $value);
    }
    /**
     * Returns the expiration time of the item.
     *
     * @return string|null Expiration time of the item or null for no expiration
     */
    public function get_time_expire(): ?string
    {
        return $this->get('expire');
    }
    /**
     * Sets the new expiration time of the item.
     *
     * @param string|null $timestamp Expiration time of the item
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function set_time_expire(?string $timestamp): \Aimeos\M_Admin\Cache\Item\Iface
    {
        return $this->set('expire', \Aimeos\Utils::datetime($timestamp));
    }
    /**
     * Returns the tags associated to the item.
     *
     * @return array Tags associated to the item
     */
    public function get_tags(): array
    {
        return $this->get('tags', []);
    }
    /**
     * Sets the new tags associated to the item.
     *
     * @param array $tags Tags associated to the item
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function set_tags(array $tags): \Aimeos\M_Admin\Cache\Item\Iface
    {
        return $this->set('tags', $tags);
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'cache.id':
                    !$private ?: $item->set_id($value);
                    break;
                case 'cache.value':
                    $item->set_value($value);
                    break;
                case 'cache.expire':
                    $item->set_time_expire($value);
                    break;
                case 'cache.tags':
                    $item->set_tags($value);
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
        $list = [];
        $list['cache.id'] = $this->get_id();
        $list['cache.value'] = $this->get_value();
        $list['cache.expire'] = $this->get_time_expire();
        $list['cache.tags'] = $this->get_tags();
        return $list;
    }
}