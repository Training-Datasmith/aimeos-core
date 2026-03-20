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
 * Default log item implementation.
 *
 * @package MAdmin
 * @subpackage Log
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Admin\Log\Item\Iface
{
    /**
     * Initializes the log item.
     *
     * @param array $values Associative list of key/value pairs
     */
    public function __construct(array $values = [])
    {
        parent::__construct('log.', $values);
    }
    /**
     * Returns the facility of the item.
     *
     * @return string Returns the facility
     */
    public function get_facility(): string
    {
        return (string) $this->get('log.facility', '');
    }
    /**
     * Sets the new facility of the of the item.
     *
     * @param string $facility Facility
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_facility(string $facility): \Aimeos\M_Admin\Log\Item\Iface
    {
        return $this->set('log.facility', $facility);
    }
    /**
     * Returns the timestamp of the item.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_timestamp(): ?string
    {
        return $this->get('log.timestamp');
    }
    /**
     * Returns the priority of the item.
     *
     * @return int Returns the priority
     */
    public function get_priority(): int
    {
        return $this->get('log.priority', 0);
    }
    /**
     * Sets the new priority of the item.
     *
     * @param int $priority Priority
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_priority(int $priority): \Aimeos\M_Admin\Log\Item\Iface
    {
        return $this->set('log.priority', $priority);
    }
    /**
     * Returns the message of the item.
     *
     * @return string Returns the message
     */
    public function get_message(): string
    {
        return $this->get('log.message', '');
    }
    /**
     * Sets the new message of the item.
     *
     * @param string $message Message
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_message(string $message): \Aimeos\M_Admin\Log\Item\Iface
    {
        return $this->set('log.message', $message);
    }
    /**
     * Returns the request of the item.
     *
     * @return string Returns the request
     */
    public function get_request(): string
    {
        return (string) $this->get('log.request', '');
    }
    /**
     * Sets the new request of the item.
     *
     * @param string $request Request
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function set_request(string $request): \Aimeos\M_Admin\Log\Item\Iface
    {
        return $this->set('log.request', $request);
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'log.facility':
                    $item->set_facility($value);
                    break;
                case 'log.priority':
                    $item->set_priority($value);
                    break;
                case 'log.message':
                    $item->set_message($value);
                    break;
                case 'log.request':
                    $item->set_request($value);
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
        $list['log.facility'] = $this->get_facility();
        $list['log.timestamp'] = $this->get_timestamp();
        $list['log.priority'] = $this->get_priority();
        $list['log.message'] = $this->get_message();
        $list['log.request'] = $this->get_request();
        return $list;
    }
}