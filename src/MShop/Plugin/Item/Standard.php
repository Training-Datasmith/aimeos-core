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
 * Default implementation of plugin items.
 *
 * @package MShop
 * @subpackage Plugin
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Plugin\Item\Iface
{
    use \Aimeos\M_Shop\Common\Item\Config\Traits;
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Returns the type of the plugin.
     * Overwritten for different default value.
     *
     * @return string Plugin type
     */
    public function get_type(): string
    {
        return $this->get('plugin.type', 'order');
    }
    /**
     * Returns the provider of the plugin.
     *
     * @return string Plugin provider which is the short plugin class name
     */
    public function get_provider(): string
    {
        return $this->get('plugin.provider', '');
    }
    /**
     * Sets the new provider of the plugin item which is the short
     * name of the plugin class name.
     *
     * @param string $provider Plugin provider, esp. short plugin class name
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
     */
    public function set_provider(string $provider): \Aimeos\M_Shop\Plugin\Item\Iface
    {
        if (preg_match('/^[A-Za-z0-9]+(,[A-Za-z0-9]+)*$/', $provider) !== 1) {
            throw new \Aimeos\M_Shop\Plugin\Exception(sprintf('Invalid provider name "%1$s"', $provider));
        }
        return $this->set('plugin.provider', $provider);
    }
    /**
     * Returns the name of the plugin item.
     *
     * @return string Label of the plugin item
     */
    public function get_label(): string
    {
        return $this->get('plugin.label', '');
    }
    /**
     * Sets the new label of the plugin item.
     *
     * @param string $label New label of the plugin item
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Plugin\Item\Iface
    {
        return $this->set('plugin.label', $label);
    }
    /**
     * Returns the position of the plugin item.
     *
     * @return int Position of the item
     */
    public function get_position(): int
    {
        return $this->get('plugin.position', 0);
    }
    /**
     * Sets the new position of the plugin item.
     *
     * @param int $position Position of the item
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
     */
    public function set_position(int $position): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('plugin.position', $position);
    }
    /**
     * Returns the status of the plugin item.
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('plugin.status', 1);
    }
    /**
     * Sets the new status of the plugin item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('plugin.status', $status);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_status() > 0;
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'plugin.type':
                    $item->set_type($value);
                    break;
                case 'plugin.label':
                    $item->set_label($value);
                    break;
                case 'plugin.provider':
                    $item->set_provider($value);
                    break;
                case 'plugin.status':
                    $item->set_status((int) $value);
                    break;
                case 'plugin.config':
                    $item->set_config((array) $value);
                    break;
                case 'plugin.position':
                    $item->set_position((int) $value);
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
        $list['plugin.type'] = $this->get_type();
        $list['plugin.label'] = $this->get_label();
        $list['plugin.provider'] = $this->get_provider();
        $list['plugin.config'] = $this->get_config();
        $list['plugin.status'] = $this->get_status();
        $list['plugin.position'] = $this->get_position();
        return $list;
    }
}