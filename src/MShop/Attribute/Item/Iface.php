<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Attribute
 */
namespace Aimeos\M_Shop\Attribute\Item;

/**
 * Generic interface for all attribute items.
 *
 * @package MShop
 * @subpackage Attribute
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Domain\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Property_Ref\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the unique key of the attribute item
     *
     * @return string Unique key consisting of domain/type/code
     */
    public function get_key(): string;
    /**
     * Returns the code of the attribute item.
     *
     * @return string Returns the code of the attribute item
     */
    public function get_code(): string;
    /**
     * Sets the code for the attribute item.
     *
     * @param string $code Code of the attribute item
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Attribute\Item\Iface;
    /**
     * Returns the name of the attribute item.
     *
     * @return string Label of the attribute item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the attribute item.
     *
     * @param string $label Type label of the attribute item
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Attribute\Item\Iface;
}