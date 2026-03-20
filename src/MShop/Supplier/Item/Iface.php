<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Supplier
 */
namespace Aimeos\M_Shop\Supplier\Item;

/**
 * Interface for supplier DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Supplier
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Address_Ref\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the label of the supplier item.
     *
     * @return string label of the supplier item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the supplier item.
     *
     * @param string $value label of the supplier item
     * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
     */
    public function set_label(string $value): \Aimeos\M_Shop\Supplier\Item\Iface;
    /**
     * Returns the code of the supplier item.
     *
     * @return string Code of the supplier item
     */
    public function get_code(): string;
    /**
     * Sets the new code of the supplier item.
     *
     * @param string $value Code of the supplier item
     * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
     */
    public function set_code(string $value): \Aimeos\M_Shop\Supplier\Item\Iface;
}