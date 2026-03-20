<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Item;

/**
 * Generic interface for plugins created and saved by plugin managers.
 *
 * @package MShop
 * @subpackage Plugin
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the name of the plugin item.
     *
     * @return string Label of the plugin item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the plugin item.
     *
     * @param string $label New label of the plugin item
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Plugin\Item\Iface;
    /**
     * Returns the provider of the plugin.
     *
     * @return string Plugin provider which is the short plugin class name
     */
    public function get_provider(): string;
    /**
     * Sets the new provider of the plugin item which is the short name of the plugin class name.
     *
     * @param string $provider Plugin provider, esp. short plugin class name
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
     */
    public function set_provider(string $provider): \Aimeos\M_Shop\Plugin\Item\Iface;
}