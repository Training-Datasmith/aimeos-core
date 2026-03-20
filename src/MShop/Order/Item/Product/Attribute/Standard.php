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
 * Default product attribute item implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Returns the ID of the site the item is stored
     *
     * @return string Site ID (or null if not available)
     */
    public function get_site_id(): string
    {
        return $this->get('order.product.attribute.siteid', '');
    }
    /**
     * Sets the site ID of the item.
     *
     * @param string $value Unique site ID of the item
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
    {
        return $this->set('order.product.attribute.siteid', $value);
    }
    /**
     * Returns the original attribute ID of the product attribute item.
     *
     * @return string Attribute ID of the product attribute item
     */
    public function get_attribute_id(): string
    {
        return $this->get('order.product.attribute.attributeid', '');
    }
    /**
     * Sets the original attribute ID of the product attribute item.
     *
     * @param string|null $id Attribute ID of the product attribute item
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_attribute_id(?string $id): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
    {
        return $this->set('order.product.attribute.attributeid', (string) $id);
    }
    /**
     * Returns the ID of the ordered product as parent
     *
     * @return string|null ID of the ordered product
     */
    public function get_parent_id(): ?string
    {
        return $this->get('order.product.attribute.parentid');
    }
    /**
     * Sets the ID of the ordered product as parent
     *
     * @param string|null $id ID of the ordered product
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_parent_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('order.product.attribute.parentid', $id);
    }
    /**
     * Returns the code of the product attibute.
     *
     * @return string Code of the attribute
     */
    public function get_code(): string
    {
        return (string) $this->get('order.product.attribute.code', '');
    }
    /**
     * Sets the code of the product attribute.
     *
     * @param string $code Code of the attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
    {
        return $this->set('order.product.attribute.code', \Aimeos\Utils::code($code, 255));
    }
    /**
     * Returns the localized name of the product attribute.
     *
     * @return string Localized name of the product attribute
     */
    public function get_name(): string
    {
        return $this->get('order.product.attribute.name', '');
    }
    /**
     * Sets the localized name of the product attribute.
     *
     * @param string|null $name Localized name of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_name(?string $name): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
    {
        return $this->set('order.product.attribute.name', (string) $name);
    }
    /**
     * Returns the value of the product attribute.
     *
     * @return string|array Value of the product attribute
     */
    public function get_value()
    {
        return $this->get('order.product.attribute.value', '');
    }
    /**
     * Sets the value of the product attribute.
     *
     * @param string|array $value Value of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_value($value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
    {
        return $this->set('order.product.attribute.value', $value);
    }
    /**
     * Returns the quantity of the product attribute.
     *
     * @return float Quantity of the product attribute
     */
    public function get_quantity(): float
    {
        return $this->get('order.product.attribute.quantity', 1);
    }
    /**
     * Sets the quantity of the product attribute.
     *
     * @param float $value Quantity of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_quantity(float $value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
    {
        return $this->set('order.product.attribute.quantity', $value);
    }
    /**
     * Returns the price of the product attribute.
     *
     * @return string|null Price of the product attribute
     */
    public function get_price(): ?string
    {
        return $this->get('order.product.attribute.price');
    }
    /**
     * Sets the price of the product attribute.
     *
     * @param string|null $value Price of the product attribute
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function set_price(?string $value): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
    {
        return $this->set('order.product.attribute.price', $value);
    }
    /**
     * Copys all data from a given attribute item.
     *
     * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Attribute\Item\Iface $item): \Aimeos\M_Shop\Order\Item\Product\Attribute\Iface
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
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order product attribute item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.product.attribute.attributeid':
                    !$private ?: $item->set_attribute_id($value);
                    break;
                case 'order.product.attribute.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'order.product.attribute.siteid':
                    !$private ?: $item->set_site_id($value);
                    break;
                case 'order.product.attribute.type':
                    $item->set_type($value);
                    break;
                case 'order.product.attribute.code':
                    $item->set_code($value);
                    break;
                case 'order.product.attribute.name':
                    $item->set_name($value);
                    break;
                case 'order.product.attribute.value':
                    $item->set_value($value);
                    break;
                case 'order.product.attribute.price':
                    $item->set_price($value);
                    break;
                case 'order.product.attribute.quantity':
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
        $list['order.product.attribute.type'] = $this->get_type();
        $list['order.product.attribute.code'] = $this->get_code();
        $list['order.product.attribute.name'] = $this->get_name();
        $list['order.product.attribute.value'] = $this->get_value();
        $list['order.product.attribute.price'] = $this->get_price();
        $list['order.product.attribute.quantity'] = $this->get_quantity();
        if ($private === true) {
            $list['order.product.attribute.parentid'] = $this->get_parent_id();
            $list['order.product.attribute.attributeid'] = $this->get_attribute_id();
        }
        return $list;
    }
}