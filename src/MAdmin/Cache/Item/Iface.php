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
 * MAdmin cache item Interface.
 *
 * @package MAdmin
 * @subpackage Cache
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface
{
    /**
     * Returns the value associated to the key.
     *
     * @return string Returns the value of the item
     */
    public function get_value(): string;
    /**
     * Sets the new value of the item.
     *
     * @param string $value Value of the item
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function set_value(string $value): \Aimeos\M_Admin\Cache\Item\Iface;
    /**
     * Returns the expiration time of the item.
     *
     * @return string|null Expiration time of the item or null for no expiration
     */
    public function get_time_expire(): ?string;
    /**
     * Sets the new expiration time of the item.
     *
     * @param string|null $timestamp Expiration time of the item or null for no expiration
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function set_time_expire(?string $timestamp): \Aimeos\M_Admin\Cache\Item\Iface;
    /**
     * Returns the tags associated to the item.
     *
     * @return array Tags associated to the item
     */
    public function get_tags(): array;
    /**
     * Sets the new tags associated to the item.
     *
     * @param array $tags Tags associated to the item
     * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
     */
    public function set_tags(array $tags): \Aimeos\M_Admin\Cache\Item\Iface;
}