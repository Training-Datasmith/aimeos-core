<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Customer
 */
namespace Aimeos\M_Shop\Group\Item;

/**
 * Interface for group objects
 *
 * @package MShop
 * @subpackage Customer
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface
{
    /**
     * Returns the code of the group
     *
     * @return string Code of the group
     */
    public function get_code(): string;
    /**
     * Sets the new code of the group
     *
     * @param string $value Code of the group
     * @return \Aimeos\MShop\Group\Item\Iface Customer group item for chaining method calls
     */
    public function set_code(string $value): \Aimeos\M_Shop\Group\Item\Iface;
    /**
     * Returns the label of the group
     *
     * @return string Label of the group
     */
    public function get_label(): string;
    /**
     * Sets the new label of the group
     *
     * @param string $value Label of the group
     * @return \Aimeos\MShop\Group\Item\Iface Customer group item for chaining method calls
     */
    public function set_label(string $value): \Aimeos\M_Shop\Group\Item\Iface;
}