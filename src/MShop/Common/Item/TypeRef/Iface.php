<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Type_Ref;

/**
 * Common interface for items having types.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Returns the type item of the item if available.
     *
     * @return \Aimeos\MShop\Type\Item\Iface|null Type item or NULL if not available
     */
    public function get_type_item(): ?\Aimeos\M_Shop\Type\Item\Iface;
    /**
     * Returns the type of the item.
     *
     * @return string Type of the item
     */
    public function get_type(): string;
    /**
     * Sets the new type of the item.
     *
     * @param string $type Type of the item
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_type(string $type): \Aimeos\M_Shop\Common\Item\Iface;
}