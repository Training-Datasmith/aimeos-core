<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Product;

/**
 * Order product item abstract class defining available flags.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\M_Shop\Common\Item\Base
{
    /**
     * No flag used.
     * No order product flag set.
     */
    public const FLAG_NONE = 0;
    /**
     * Product is immutable.
     * Ordered product can't be modifed or deleted by the customer because it
     * was e.g. added by a coupon provider.
     */
    public const FLAG_IMMUTABLE = 1;
    private ?array $attributes_map = null;
    private array $attr_rm_items = [];
    /**
     * Adds new and replaces existing attribute items for the product.
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Product\Attribute\Iface[] $attributes List of order product attribute items
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function add_attribute_items(iterable $attributes): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        map($attributes)->implements(\Aimeos\M_Shop\Order\Item\Product\Attribute\Iface::class, true);
        foreach ($attributes as $attr_item) {
            $this->set_attribute_item($attr_item);
        }
        return $this;
    }
    /**
     * Returns the value or list of values of the attribute item for the ordered product with the given code.
     *
     * @param string $code Code of the product attribute item
     * @param array|string $type Type or list of types of the product attribute items
     * @return array|string|null Value or list of values of the attribute item for the ordered product and the given code
     */
    public function get_attribute(string $code, $type = '')
    {
        $list = [];
        $map = $this->get_attribute_map();
        foreach ((array) $type as $key) {
            if (isset($map[$key][$code])) {
                foreach ($map[$key][$code] as $item) {
                    $list[] = $item->get_value();
                }
            }
        }
        return count($list) > 1 ? $list : (reset($list) ?: null);
    }
    /**
     * Returns the attribute item or list of attribute items for the ordered product with the given code.
     *
     * @param string $code Code of the product attribute item
     * @param array|string $type Type of the product attribute item
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface|array|null
     * 	Attribute item or list of items for the ordered product and the given code
     */
    public function get_attribute_item(string $code, $type = '')
    {
        $list = [];
        $map = $this->get_attribute_map();
        foreach ((array) $type as $key) {
            if (isset($map[$key][$code])) {
                foreach ($map[$key][$code] as $item) {
                    $list[] = $item;
                }
            }
        }
        return count($list) > 1 ? $list : (reset($list) ?: null);
    }
    /**
     * Returns the list of attribute items for the ordered product.
     *
     * @param array|string|null $type Filters returned attributes by the given types, type or null for no filtering
     * @return \Aimeos\Map List of attribute items implementing \Aimeos\MShop\Order\Item\Product\Attribute\Iface
     */
    public function get_attribute_items($type = null): \Aimeos\Map
    {
        if ($type === null) {
            return map($this->get('.attributes', []));
        }
        $list = [];
        foreach ($this->get('.attributes', []) as $attr_item) {
            if (in_array($attr_item->get_type(), (array) $type)) {
                $list[] = $attr_item;
            }
        }
        return map($list);
    }
    /**
     * Returns the deleted attribute items for the service.
     *
     * @return \Aimeos\Map List of items to be removed
     */
    public function get_attribute_items_deleted(): \Aimeos\Map
    {
        return map($this->attr_rm_items);
    }
    /**
     * Adds or replaces the attribute item in the list of service attributes.
     *
     * @param \Aimeos\MShop\Order\Item\Product\Attribute\Iface $item Service attribute item
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_attribute_item(\Aimeos\M_Shop\Order\Item\Product\Attribute\Iface $item): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        $this->get_attribute_map();
        $type = $item->get_type();
        $code = $item->get_code();
        $attr_id = $item->get_attribute_id();
        if (!isset($this->attributes_map[$type][$code][$attr_id])) {
            $this->set('.attributes', map($this->get('.attributes', []))->push($item));
            $this->attributes_map[$type][$code][$attr_id] = $item;
        }
        $this->attributes_map[$type][$code][$attr_id]->set_value($item->get_value());
        return $this->set_modified();
    }
    /**
     * Sets the new list of attribute items for the product.
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Product\Attribute\Iface[] $attributes List of order product attribute items
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_attribute_items(iterable $attributes): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        ($attributes = map($attributes))->implements(\Aimeos\M_Shop\Order\Item\Product\Attribute\Iface::class, true);
        $this->attr_rm_items = map($this->get('.attributes', []))->diff($attributes)->merge($this->attr_rm_items)->unique()->to_array();
        $this->set('.attributes', $attributes->to_array());
        $this->attributes_map = null;
        return $this;
    }
    /**
     * Checks if the given flag constant is valid.
     *
     * @param int $value Flag constant value
     */
    protected function check_flags(int $value)
    {
        if ($value < \Aimeos\M_Shop\Order\Item\Product\Base::FLAG_NONE || $value > \Aimeos\M_Shop\Order\Item\Product\Base::FLAG_IMMUTABLE) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Flags "%1$s" not within allowed range', $value));
        }
        return $value;
    }
    /**
     * Returns the attribute map for the ordered products.
     *
     * @return array Associative list of type and code as key and an \Aimeos\MShop\Order\Item\Product\Attribute\Iface as value
     */
    protected function get_attribute_map(): array
    {
        if (!isset($this->attributes_map)) {
            $this->attributes_map = [];
            foreach ($this->get('.attributes', []) as $item) {
                $this->attributes_map[$item->get_type()][$item->get_code()][$item->get_attribute_id()] = $item;
            }
        }
        return $this->attributes_map;
    }
}