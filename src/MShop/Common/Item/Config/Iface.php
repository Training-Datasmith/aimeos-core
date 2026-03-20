<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Config;

/**
 * Common interface for items containing configuration
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Returns the configuration values of the item
     *
     * @return array Configuration values
     */
    public function get_config(): array;
    /**
     * Returns the configuration value for the specified path
     *
     * If your configuration looks like
     *  [
     *    'path' => [
     *      'to' => 'value'
     *    ]
     *  ]
     *  you can get "value" by using "path/to" as key.
     *
     * @param string $key Key of the associative array or path to value like "path/to/value"
     * @param mixed $default Default value if no configration is found
     * @return mixed Configuration value or array of values
     */
    public function get_config_value(string $key, $default = null);
    /**
     * Sets the configuration values of the item
     *
     * @param array $config Configuration values
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_config(array $config): \Aimeos\M_Shop\Common\Item\Iface;
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
     *
     * @param string $key Key of the associative array or path to value like "path/to/value"
     * @param mixed $value Value to set for the key
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_config_value(string $key, $value): \Aimeos\M_Shop\Common\Item\Iface;
}