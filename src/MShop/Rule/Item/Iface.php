<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Item;

/**
 * Generic interface for rules created and saved by rule managers.
 *
 * @package MShop
 * @subpackage Rule
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Time\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the name of the rule item.
     *
     * @return string Label of the rule item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the rule item.
     *
     * @param string $label New label of the rule item
     * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Rule\Item\Iface;
    /**
     * Returns the provider of the rule.
     *
     * @return string Rule provider which is the short rule class name
     */
    public function get_provider(): string;
    /**
     * Sets the new provider of the rule item which is the short name of the rule class name.
     *
     * @param string $provider Rule provider, esp. short rule class name
     * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
     */
    public function set_provider(string $provider): \Aimeos\M_Shop\Rule\Item\Iface;
}