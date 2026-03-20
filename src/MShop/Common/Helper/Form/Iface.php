<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Helper\Form;

/**
 * Generic interface for the form helper
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Returns if the URL points to an external site.
     *
     * @return bool True if URL points to an external site, false if it stays on the same site
     */
    public function get_external(): bool;
    /**
     * Sets if the URL points to an external site.
     *
     * @param bool $value True if URL points to an external site, false if it stays on the same site
     * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
     */
    public function set_external(bool $value): \Aimeos\M_Shop\Common\Helper\Form\Iface;
    /**
     * Returns the custom HTML string.
     *
     * @return string HTML string
     */
    public function get_html(): string;
    /**
     * Sets the custom HTML string.
     *
     * @param string $html HTML string
     * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
     */
    public function set_html(string $html): \Aimeos\M_Shop\Common\Helper\Form\Iface;
    /**
     * Returns the method.
     *
     * @return string Method
     */
    public function get_method(): string;
    /**
     * Sets the method.
     *
     * @param string $method Method
     * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
     */
    public function set_method(string $method): \Aimeos\M_Shop\Common\Helper\Form\Iface;
    /**
     * Returns the url.
     *
     * @return string Url
     */
    public function get_url(): string;
    /**
     * Sets the url.
     *
     * @param string $url Url
     * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
     */
    public function set_url(string $url): \Aimeos\M_Shop\Common\Helper\Form\Iface;
    /**
     * Returns the value for the given key.
     *
     * @param string $key Unique key
     * @return \Aimeos\Base\Criteria\Attribute\Iface Attribute item for the given key
     */
    public function get_value(string $key): \Aimeos\Base\Criteria\Attribute\Iface;
    /**
     * Sets the value for the key.
     *
     * @param string $key Unique key
     * @param \Aimeos\Base\Criteria\Attribute\Iface $value Attribute item for the given key
     * @return \Aimeos\MShop\Common\Helper\Form\Iface Helper for chaining method calls
     */
    public function set_value(string $key, \Aimeos\Base\Criteria\Attribute\Iface $value): \Aimeos\M_Shop\Common\Helper\Form\Iface;
    /**
     * Returns the all key/value pairs.
     *
     * @return array Key/value pairs, values implementing \Aimeos\Base\Criteria\Attribute\Iface
     */
    public function get_values(): array;
}