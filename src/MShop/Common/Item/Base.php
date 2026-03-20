<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item;

/**
 * Common methods for all item objects.
 *
 * @package MShop
 * @subpackage Common
 */
class Base implements \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\Macro\Iface, \ArrayAccess, \JsonSerializable
{
    use \Aimeos\Macro\Macroable;
    // protected due to PHP serialization
    protected bool $available = true;
    protected bool $modified = false;
    /**
     * Initializes the class properties.
     *
     * @param string $bprefix Prefix for the keys returned by toArray()
     * @param array $bdata Associative list of key/value pairs of the item properties
     * @param string|null $type Item resource type
     */
    public function __construct(protected string $bprefix, protected array $bdata = [], protected ?string $type = null)
    {
    }
    /**
     * Returns the item property for the given name
     *
     * @param string $name Name of the property
     * @return mixed|null Property value or null if property is unknown
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }
    /**
     * Tests if the item property for the given name is available
     *
     * @param string $name Name of the property
     * @return bool True if the property exists, false if not
     */
    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->bdata);
    }
    /**
     * Sets the new item property for the given name
     *
     * @param string $name Name of the property
     * @param mixed $value New property value
     */
    public function __set(string $name, $value)
    {
        $this->set($name, $value);
    }
    /**
     * Specifies the data which should be serialized to JSON by json_encode().
     *
     * @return array<string,mixed> Data to serialize to JSON
     */
    #[\Return_Type_Will_Change]
    public function jsonSerialize()
    {
        return $this->bdata;
    }
    /**
     * Tests if the item property for the given name is available
     *
     * @param string $name Name of the property
     * @return bool True if the property exists, false if not
     */
    public function offsetExists($name): bool
    {
        return array_key_exists($name, $this->bdata);
    }
    /**
     * Returns the item property for the given name
     *
     * @param string $name Name of the property
     * @return mixed|null Property value or null if property is unknown
     */
    #[\Return_Type_Will_Change]
    public function offsetGet($name)
    {
        return $this->get($name);
    }
    /**
     * Sets the new item property for the given name
     *
     * @param string $name Name of the property
     * @param mixed $value New property value
     */
    public function offsetSet($name, $value): void
    {
        $this->set($name, $value);
    }
    /**
     * Removes an item property
     * This is not supported by items
     *
     * @param string $name Name of the property
     * @throws \LogicException Always thrown because this method isn't supported
     */
    public function offsetUnset($name): void
    {
        throw new \LogicException('Not implemented');
    }
    /**
     * Returns the ID of the items
     *
     * @return string ID of the item or an empty string
     */
    public function __toString(): string
    {
        return (string) $this->get_id();
    }
    /**
     * Assigns multiple key/value pairs to the item
     *
     * @param iterable $pairs Associative list of key/value pairs
     * @return \Aimeos\MShop\Common\Item\Iface Item for method chaining
     */
    public function assign(iterable $pairs): \Aimeos\M_Shop\Common\Item\Iface
    {
        foreach ($pairs as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }
    /**
     * Returns the item property for the given name
     *
     * @param string $name Name of the property
     * @param mixed $default Default value if property is unknown
     * @return mixed|null Property value or default value if property is unknown
     */
    public function get(string $name, $default = null)
    {
        if (array_key_exists($name, $this->bdata)) {
            return $this->bdata[$name];
        }
        return $default;
    }
    /**
     * Sets the new item property for the given name
     *
     * @param string $name Name of the property
     * @param mixed $value New property value
     * @return \Aimeos\MShop\Common\Item\Iface Item for method chaining
     */
    public function set(string $name, $value): \Aimeos\M_Shop\Common\Item\Iface
    {
        // workaround for NULL values instead of empty strings and stringified integers from database
        if (!array_key_exists($name, $this->bdata) || $this->bdata[$name] != $value || $value === null && $this->bdata[$name] !== null || $value !== null && $this->bdata[$name] === null) {
            $this->bdata[$name] = $value;
            $this->set_modified();
        }
        return $this;
    }
    /**
     * Returns the ID of the item if available.
     *
     * @return string|null ID of the item
     */
    public function get_id(): ?string
    {
        $key = $this->bprefix . 'id';
        if (isset($this->bdata[$key]) && $this->bdata[$key] != '') {
            return (string) $this->bdata[$key];
        }
        return null;
    }
    /**
     * Sets the new ID of the item.
     *
     * @param string|null $id ID of the item
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->bdata[$this->bprefix . 'id'] = $id;
        $this->modified = $id === null;
        return $this;
    }
    /**
     * Returns the site ID of the item.
     *
     * @return string Site ID or null if no site id is available
     */
    public function get_site_id(): string
    {
        return $this->get($this->bprefix . 'siteid', $this->get('siteid', ''));
    }
    /**
     * Returns the list site IDs up to the root site item.
     *
     * @return array List of site IDs
     */
    public function get_site_path(): array
    {
        $pos = 0;
        $list = [];
        $site_id = $this->get_site_id();
        while (($pos = strpos($site_id, '.', $pos)) !== false) {
            $list[] = substr($site_id, 0, ++$pos);
        }
        return $list;
    }
    /**
     * Returns modify date/time of the order coupon.
     *
     * @return string|null Modification time (YYYY-MM-DD HH:mm:ss)
     */
    public function get_time_modified(): ?string
    {
        return $this->get($this->bprefix . 'mtime', $this->get('mtime'));
    }
    /**
     * Returns the create date of the item.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_time_created(): ?string
    {
        return $this->get($this->bprefix . 'ctime', $this->get('ctime'));
    }
    /**
     * Returns the name of editor who created/modified the item at last.
     *
     * @return string Name of editor who created/modified the item at last
     */
    public function editor(): string
    {
        return $this->get($this->bprefix . 'editor', $this->get('editor', ''));
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return $this->available;
    }
    /**
     * Sets the general availability of the item
     *
     * @return bool $value True if available, false if not
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_available(bool $value): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->available = $value;
        return $this;
    }
    /**
     * Tests if this Item object was modified.
     *
     * @return bool True if modified, false if not
     */
    public function is_modified(): bool
    {
        return $this->modified;
    }
    /**
     * Sets the modified flag of the object.
     *
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_modified(): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->modified = true;
        return $this;
    }
    /**
     * Returns the item type
     *
     * @return string Item type, subtypes are separated by slashes
     */
    public function get_resource_type(): string
    {
        if (!$this->type) {
            $parts = explode('\\', strtolower(static::class));
            array_shift($parts);
            array_shift($parts);
            // remove "Aimeos\MShop"
            array_pop($parts);
            $domain = array_shift($parts) ?: 'custom';
            array_shift($parts);
            // remove "item"
            array_unshift($parts, $domain);
            $this->type = join('/', $parts);
        }
        return $this->type;
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array $list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        if ($private && array_key_exists($this->bprefix . 'id', $list)) {
            $this->set_id($list[$this->bprefix . 'id']);
            unset($list[$this->bprefix . 'id']);
        }
        // Add custom columns
        foreach ($list as $key => $value) {
            if ((is_null($value) || is_scalar($value) || is_array($value)) && !str_contains($key, '.')) {
                $this->set($key, $value);
            }
        }
        return $this;
    }
    /**
     * Returns the item values as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $list = [$this->bprefix . 'id' => $this->get_id()];
        if ($private === true) {
            $list[$this->bprefix . 'siteid'] = $this->get_site_id();
            $list[$this->bprefix . 'ctime'] = $this->get_time_created();
            $list[$this->bprefix . 'mtime'] = $this->get_time_modified();
            $list[$this->bprefix . 'editor'] = $this->editor();
        }
        foreach ($this->bdata as $key => $value) {
            if (!str_contains($key, '.')) {
                $list[$key] = $value;
            }
        }
        return $list;
    }
    /**
     * Returns the prefix for the item properties
     *
     * @return string Prefix for the item properties
     */
    protected function prefix(): string
    {
        return $this->bprefix;
    }
}