<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Service;

/**
 * Order service item abstract class defining available types.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\M_Shop\Common\Item\Base implements Iface
{
    /**
     * Delivery service.
     */
    public const TYPE_DELIVERY = 'delivery';
    /**
     * Payment service.
     */
    public const TYPE_PAYMENT = 'payment';
    private ?array $attributes_map = null;
    private array $attr_rm_items = [];
    /**
     * Adds new and replaces existing attribute items for the service.
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Service\Attribute\Iface[] $attributes List of order service attribute items
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function add_attribute_items(iterable $attributes): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        map($attributes)->implements(\Aimeos\M_Shop\Order\Item\Service\Attribute\Iface::class, true);
        foreach ($attributes as $attr_item) {
            $this->set_attribute_item($attr_item);
        }
        return $this;
    }
    /**
     * Returns the value or list of values of the attribute item for the ordered service with the given code.
     *
     * @param string $code Code of the service attribute item
     * @param array|string $type Type or list of types of the service attribute items
     * @return array|string|null Value or list of values of the attribute item for the ordered service and the given code
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
     * Returns the attribute item or list of attribute items for the ordered service with the given code.
     *
     * @param string $code Code of the service attribute item
     * @param array|string $type Type of the service attribute item
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface|array|null
     * 	Attribute item or list of items for the ordered service and the given code
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
     * Returns the list of attribute items for the ordered service.
     *
     * @param array|string|null $type Filters returned attributes by the given types, type or null for no filtering
     * @return \Aimeos\Map List of attribute items implementing \Aimeos\MShop\Order\Item\Service\Attribute\Iface
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
     * @param \Aimeos\MShop\Order\Item\Service\Attribute\Iface $item Service attribute item
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_attribute_item(\Aimeos\M_Shop\Order\Item\Service\Attribute\Iface $item): \Aimeos\M_Shop\Order\Item\Service\Iface
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
     * Sets the new list of attribute items for the service.
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Service\Attribute\Iface[] $attributes List of order service attribute items
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_attribute_items(iterable $attributes): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        ($attributes = map($attributes))->implements(\Aimeos\M_Shop\Order\Item\Service\Attribute\Iface::class, true);
        $this->attr_rm_items = map($this->get('.attributes', []))->diff($attributes)->merge($this->attr_rm_items)->unique()->to_array();
        $this->set('.attributes', $attributes->to_array());
        $this->attributes_map = null;
        return $this;
    }
    /**
     * Adds a new transaction to the service.
     *
     * @param \Aimeos\MShop\Order\Item\Service\Transaction\Iface $item Transaction item
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function add_transaction(\Aimeos\M_Shop\Order\Item\Service\Transaction\Iface $item): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('.transactions', map($this->get('.transactions', []))->push($item));
    }
    /**
     * Returns the list of transactions items for the service.
     *
     * @param string|null $type Filters returned transactions by the given type or null for no filtering
     * @return \Aimeos\Map List of transaction items implementing \Aimeos\MShop\Order\Item\Service\Attribute\Iface
     */
    public function get_transactions(?string $type = null): \Aimeos\Map
    {
        return map($this->get('.transactions', []));
    }
    /**
     * Sets the new list of transactions items for the service.
     *
     * @param iterable $list List of order service transaction items
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_transactions(iterable $list): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('.transactions', $list);
    }
    /**
     * Returns the attribute map for the ordered services.
     *
     * @return array Associative list of type and code as key and an \Aimeos\MShop\Order\Item\Service\Attribute\Iface as value
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