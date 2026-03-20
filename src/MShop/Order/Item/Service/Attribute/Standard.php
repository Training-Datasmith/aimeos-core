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
 * Default order item base service attribute.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Returns the ID of the site the item is stored
     *
     * @return string Site ID (or null if not available)
     */
    public function get_site_id(): string
    {
        return $this->get('order.service.attribute.siteid', '');
    }
    /**
     * Sets the site ID of the item.
     *
     * @param string $value Unique site ID of the item
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        return $this->set('order.service.attribute.siteid', $value);
    }
    /**
     * Returns the original attribute ID of the service attribute item.
     *
     * @return string Attribute ID of the service attribute item
     */
    public function get_attribute_id(): string
    {
        return $this->get('order.service.attribute.attributeid', '');
    }
    /**
     * Sets the original attribute ID of the service attribute item.
     *
     * @param string|null $id Attribute ID of the service attribute item
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_attribute_id(?string $id): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        return $this->set('order.service.attribute.attributeid', (string) $id);
    }
    /**
     * Returns the ID of the ordered service item as parent
     *
     * @return string|null ID of the ordered service item
     */
    public function get_parent_id(): ?string
    {
        return $this->get('order.service.attribute.parentid');
    }
    /**
     * Sets the ID of the ordered service item as parent
     *
     * @param string|null $id ID of the ordered service item
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_parent_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('order.service.attribute.parentid', $id);
    }
    /**
     * Returns the code of the service attribute item.
     *
     * @return string Code of the service attribute item
     */
    public function get_code(): string
    {
        return $this->get('order.service.attribute.code', '');
    }
    /**
     * Sets a new code for the service attribute item.
     *
     * @param string $code Code as defined by the service provider
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        return $this->set('order.service.attribute.code', \Aimeos\Utils::code($code, 255));
    }
    /**
     * Returns the name of the service attribute item.
     *
     * @return string Name of the service attribute item
     */
    public function get_name(): string
    {
        return $this->get('order.service.attribute.name', '');
    }
    /**
     * Sets a new name for the service attribute item.
     *
     * @param string|null $name Name as defined by the service provider
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_name(?string $name): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        return $this->set('order.service.attribute.name', (string) $name);
    }
    /**
     * Returns the value of the service attribute item.
     *
     * @return string|array Service attribute item value
     */
    public function get_value()
    {
        return $this->get('order.service.attribute.value', '');
    }
    /**
     * Sets a new value for the service item.
     *
     * @param string|array $value service attribute item value
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_value($value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        return $this->set('order.service.attribute.value', $value);
    }
    /**
     * Returns the quantity of the service attribute.
     *
     * @return float Quantity of the service attribute
     */
    public function get_quantity(): float
    {
        return $this->get('order.service.attribute.quantity', 1);
    }
    /**
     * Sets the quantity of the service attribute.
     *
     * @param float $value Quantity of the service attribute
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_quantity(float $value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        return $this->set('order.service.attribute.quantity', $value);
    }
    /**
     * Returns the price of the service attribute.
     *
     * @return string|null Price of the service attribute
     */
    public function get_price(): ?string
    {
        return $this->get('order.service.attribute.price');
    }
    /**
     * Sets the price of the service attribute.
     *
     * @param string|null $value Price of the service attribute
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function set_price(?string $value): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        return $this->set('order.service.attribute.price', $value);
    }
    /**
     * Copys all data from a given attribute item.
     *
     * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order base service attribute item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Attribute\Item\Iface $item): \Aimeos\M_Shop\Order\Item\Service\Attribute\Iface
    {
        $this->set_site_id($item->get_site_id());
        $this->set_attribute_id($item->get_id());
        $this->set_name($item->get_name());
        $this->set_code($item->get_type());
        $this->set_value($item->get_code());
        $this->set_modified();
        return $this;
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface Order service attribute item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.service.attribute.attributeid':
                    !$private ?: $item->set_attribute_id($value);
                    break;
                case 'order.service.attribute.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'order.service.attribute.siteid':
                    !$private ?: $item->set_site_id($value);
                    break;
                case 'order.service.attribute.type':
                    $item->set_type($value);
                    break;
                case 'order.service.attribute.name':
                    $item->set_name($value);
                    break;
                case 'order.service.attribute.code':
                    $item->set_code($value);
                    break;
                case 'order.service.attribute.value':
                    $item->set_value($value);
                    break;
                case 'order.service.attribute.price':
                    $item->set_price($value);
                    break;
                case 'order.service.attribute.quantity':
                    $item->set_quantity($value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns the item values as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $list = parent::to_array($private);
        $list['order.service.attribute.type'] = $this->get_type();
        $list['order.service.attribute.name'] = $this->get_name();
        $list['order.service.attribute.code'] = $this->get_code();
        $list['order.service.attribute.value'] = $this->get_value();
        $list['order.service.attribute.price'] = $this->get_price();
        $list['order.service.attribute.quantity'] = $this->get_quantity();
        if ($private === true) {
            $list['order.service.attribute.parentid'] = $this->get_parent_id();
            $list['order.service.attribute.attributeid'] = $this->get_attribute_id();
        }
        return $list;
    }
}