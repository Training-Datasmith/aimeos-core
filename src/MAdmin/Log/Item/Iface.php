<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MAdmin
 * @subpackage Log
 */
namespace Aimeos\M_Admin\Log\Item;

/**
 * MAdmin log item Interface.
 *
 * @package MAdmin
 * @subpackage Log
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface
{
    /**
     * Returns the facility of the item.
     *
     * @return string Returns the facility of the item
     */
    public function get_facility(): string;
    /**
     * Sets the new facility of the item.
     *
     * @param string $facility Facility of the item
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_facility(string $facility): \Aimeos\M_Admin\Log\Item\Iface;
    /**
     * Returns the timestamp of the item.
     *
     * @return string|null Returns the timestamp of the item
     */
    public function get_timestamp(): ?string;
    /**
     * Returns the priority of the item.
     *
     * @return int Returns the priority of the item
     */
    public function get_priority(): int;
    /**
     * Sets the new priority of the item.
     *
     * @param int $priority Priority of the item
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_priority(int $priority): \Aimeos\M_Admin\Log\Item\Iface;
    /**
     * Returns the message of the item.
     *
     * @return string Returns the message of the item
     */
    public function get_message(): string;
    /**
     * Sets the new message of the item.
     *
     * @param string $message Message of the item
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_message(string $message): \Aimeos\M_Admin\Log\Item\Iface;
    /**
     * Returns the request of the item.
     *
     * @return string Returns the request of the item
     */
    public function get_request(): string;
    /**
     * Sets the new request of the item.
     *
     * @param string $request Request of the item
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_request(string $request): \Aimeos\M_Admin\Log\Item\Iface;
}