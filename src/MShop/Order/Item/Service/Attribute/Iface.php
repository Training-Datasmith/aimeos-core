<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Service\Attribute;

/**
 * Interface for order item base service attribute.
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
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
    /**
     * Returns the original attribute ID of the ordered service attribute.
     *
     * @return string Attribute ID of the ordered service attribute
     */
    public function get_attribute_id(): string;
    /**
     * Sets the original attribute ID of the ordered service attribute.
     *
     * @param string|null $id Attribute ID of the ordered service attribute
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_attribute_id(?string $id): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
    /**
     * Returns the code of the service attribute item.
     *
     * @return string code of the service attribute item
     */
    public function get_code(): string;
    /**
     * Sets a new code for the service attribute item.
     *
     * @param string $code Code as defined by the service provider
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
    /**
     * Returns the name of the service attribute item.
     *
     * @return string Name of the service attribute item
     */
    public function get_name(): string;
    /**
     * Sets a new name for the service attribute item.
     *
     * @param string|null $name Name as defined by the service provider
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_name(?string $name): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
    /**
     * Returns the value of the service attribute item.
     *
     * @return string|array Service attribute item value
     */
    public function get_value();
    /**
     * Sets a new value for the service attribute item.
     *
     * @param string|array $value Service attribute item value
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_value($value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
    /**
     * Returns the quantity of the service attribute.
     *
     * @return float Quantity of the service attribute
     */
    public function get_quantity(): float;
    /**
     * Sets the quantity of the service attribute.
     *
     * @param float $value Quantity of the service attribute
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_quantity(float $value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
    /**
     * Returns the price of the service attribute.
     *
     * @return string|null Price of the service attribute
     */
    public function get_price(): ?string;
    /**
     * Sets the price of the service attribute.
     *
     * @param string|null $value Price of the service attribute
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_price(?string $value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
    /**
     * Copys all data from a given attribute item.
     *
     * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Attribute\Item\Iface $item): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface;
}