<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Config;

/**
 * Common trait for items containing configurations
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
    /**
     * Returns the prefix for the item properties
     *
     * @return string Prefix for the item properties
     */
    abstract protected function prefix(): string;
    /**
     * Returns the config property of the catalog.
     *
     * @return array Returns the config of the catalog node
     */
    public function get_config(): array
    {
        return (array) $this->get($this->prefix() . 'config', []);
    }
    /**
     * Sets the config property of the catalog item.
     *
     * @param array $config Configuration to be set for the catalog node
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_config(array $config): \Aimeos\M_Shop\Common\Item\Iface
    {
        if (!$this->compare_config($this->get_config(), $config)) {
            $this->set($this->prefix() . 'config', $config);
        }
        return $this;
    }
    /**
     * Returns the configuration value for the specified path
     *
     * @param string $key Key of the associative array or path to value like "path/to/value"
     * @param mixed $default Default value if no configration is found
     * @return mixed Configuration value or array of values
     */
    public function get_config_value(string $key, $default = null)
    {
        return $this->get_array_value($this->get_config(), explode('/', trim($key, '/')), $default);
    }
    /**
     * Sets all configuration values at once
     *
     * @param array $flat Associative list of keys (with "/" for nested arrays) and values
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_config_flat(array $flat): \Aimeos\M_Shop\Common\Item\Iface
    {
        $config = [];
        foreach ($flat as $key => $value) {
            $config = $this->set_array_value($config, explode('/', trim($key, '/')), $value);
        }
        if (!$this->compare_config($this->get_config(), $config)) {
            return $this->set_config($config);
        }
        return $this;
    }
    /**
     * Sets the configuration value for the specified path
     *
     *  Setting "value" by using "path/to" as key would result in:
     *  [
     *    'path' => [
     *      'to' => 'value'
     *    ]
     *  ]
     *
     * @param string $key Key of the associative array or path to value like "path/to/value"
     * @param mixed $value Value to set for the key
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_config_value(string $key, $value): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set_config($this->set_array_value($this->get_config(), explode('/', trim($key, '/')), $value));
    }
    /**
     * Returns if two associative arrays with string keys are equal
     *
     * @param array $a First associative array
     * @param array $b Second associative array
     * @return bool TRUE if arrays are loosly equal, FALSE if there are differences other than the order of keys
     */
    protected function compare_config(array $a, array $b): bool
    {
        if (count($a) !== count($b) || array_diff_key($a, $b)) {
            return false;
        }
        foreach ($a as $k => $v) {
            $bv = $b[$k];
            if (is_array($v) && is_array($bv)) {
                if (!$this->compare_config($v, $bv)) {
                    return false;
                }
            } elseif ($v != $bv) {
                return false;
            }
        }
        return true;
    }
    /**
     * Returns a configuration value from an array
     *
     * @param array $config The array to search in
     * @param array $parts Configuration path parts to look for inside the array
     * @param mixed $default Default value if no configuration is found
     * @return mixed Found value or null if no value is available
     */
    protected function get_array_value(array $config, array $parts, $default)
    {
        if (($current = array_shift($parts)) !== null && isset($config[$current])) {
            if (count($parts) > 0) {
                if (is_array($config[$current])) {
                    return $this->get_array_value($config[$current], $parts, $default);
                }
                return $default;
            }
            return $config[$current];
        }
        return $default;
    }
    /**
     * Sets the value for the given key parts in the array configuration
     *
     * @param array $config The configuration array to set the key/value pair in
     * @param array $parts Configuration path parts to use in the array
     * @param mixed $value Value to set in the configuration array
     * @return array Modified configuration array
     */
    protected function set_array_value(array $config, array $parts, $value): array
    {
        $current = array_shift($parts);
        if (!empty($parts)) {
            $config[$current] = $this->set_array_value($config[$current] ?? [], $parts, $value);
        } else {
            $config[$current] = $value;
        }
        return $config;
    }
}