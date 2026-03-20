<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Property;

/**
 * Common property item interface
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface, \Aimeos\M_Shop\Common\Item\Parentid\Iface
{
    /**
     * Returns the unique key of the property item
     *
     * @return string Unique key consisting of type/language/value
     */
    public function get_key(): string;
    /**
     * Returns the language id of the property item
     *
     * @return string|null Language ID of the property item
     */
    public function get_language_id(): ?string;
    /**
     * Sets the Language Id of the property item
     *
     * @param string|null $id New Language ID of the property item
     * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
     */
    public function set_language_id(?string $id): \Aimeos\M_Shop\Common\Item\Property\Iface;
    /**
     * Returns the value of the property item.
     *
     * @return string Value of the property item
     */
    public function get_value(): string;
    /**
     * Sets the new value of the property item.
     *
     * @param string $value Value of the property item
     * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
     */
    public function set_value(?string $value): \Aimeos\M_Shop\Common\Item\Property\Iface;
}