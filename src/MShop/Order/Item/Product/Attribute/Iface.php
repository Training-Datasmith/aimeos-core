<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Product\Attribute;

/**
 * Interface for objects storing the selected product attributes.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Parentid\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Sets the site ID of the item.
     *
     * @param string $value Unique site ID of the item
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
    /**
     * Returns the original attribute ID of the ordered product attribute.
     *
     * @return string Attribute ID of the ordered product attribute
     */
    public function get_attribute_id(): string;
    /**
     * Sets the original attribute ID of the ordered product attribute.
     *
     * @param string|null $id Attribute ID of the ordered product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_attribute_id(?string $id): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
    /**
     * Returns the code of the product attibute.
     *
     * @return string Code of the attribute
     */
    public function get_code(): string;
    /**
     * Sets the code of the product attribute.
     *
     * @param string $code Code of the attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
    /**
     * Returns the localized name of the product attribute.
     *
     * @return string Localized name of the product attribute
     */
    public function get_name(): string;
    /**
     * Sets the localized name of the product attribute.
     *
     * @param string|null $name Localized name of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_name(?string $name): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
    /**
     * Returns the value of the product attribute.
     *
     * @return string|array Value of the product attribute
     */
    public function get_value();
    /**
     * Sets the value of the product attribute.
     *
     * @param string|array $value Value of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_value($value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
    /**
     * Returns the quantity of the product attribute.
     *
     * @return float Quantity of the product attribute
     */
    public function get_quantity(): float;
    /**
     * Sets the quantity of the product attribute.
     *
     * @param float $value Quantity of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_quantity(float $value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
    /**
     * Returns the price of the product attribute.
     *
     * @return string|null Price of the product attribute
     */
    public function get_price(): ?string;
    /**
     * Sets the price of the product attribute.
     *
     * @param string|null $value Price of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_price(?string $value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
    /**
     * Copys all data from a given attribute item.
     *
     * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Attribute\Item\Iface $item): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface;
}