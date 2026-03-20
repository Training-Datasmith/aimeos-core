<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Attribute
 */
namespace Aimeos\M_Shop\Attribute\Item;

use Aimeos\M_Shop\Common\Item\Lists_Ref;
use Aimeos\M_Shop\Common\Item\Property_Ref;
use Aimeos\M_Shop\Common\Item\Type_Ref;
/**
 * Default attribute item implementation.
 *
 * @package MShop
 * @subpackage Attribute
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Attribute\Item\Iface
{
    use Lists_Ref\Traits, Property_Ref\Traits, Type_Ref\Traits {
        Lists_Ref\Traits::__clone as __cloneList;
        Property_Ref\Traits::__clone as __cloneProperty;
    }
    /**
     * Initializes the attribute item.
     *
     * @param string $prefix Domain specific prefix string
     * @param array $values Initial values for the item
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values);
        $this->init_list_items($values['.listitems'] ?? []);
        $this->init_property_items($values['.propitems'] ?? []);
    }
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        parent::__clone();
        $this->__clone_list();
        $this->__clone_property();
    }
    /**
     * Returns the unique key of the attribute item
     *
     * @return string Unique key consisting of domain/type/code
     */
    public function get_key(): string
    {
        return substr($this->get_domain() . '|' . $this->get_type() . '|' . $this->get_code(), 0, 255);
    }
    /**
     * Returns the domain of the attribute item.
     *
     * @return string Returns the domain for this item e.g. text, media, price...
     */
    public function get_domain(): string
    {
        return (string) $this->get('attribute.domain', '');
    }
    /**
     * Set the name of the domain for this attribute item.
     *
     * @param string $domain Name of the domain e.g. text, media, price...
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function set_domain(string $domain): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('attribute.domain', $domain);
    }
    /**
     * Returns a unique code of the attribute item.
     *
     * @return string Returns the code of the attribute item
     */
    public function get_code(): string
    {
        return (string) $this->get('attribute.code', '');
    }
    /**
     * Sets a unique code for the attribute item.
     *
     * @param string $code Code of the attribute item
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Attribute\Item\Iface
    {
        return $this->set('attribute.code', \Aimeos\Utils::code($code, 255));
    }
    /**
     * Returns the name of the attribute item.
     *
     * @return string Label of the attribute item
     */
    public function get_label(): string
    {
        return $this->get('attribute.label', '');
    }
    /**
     * Sets the new label of the attribute item.
     *
     * @param string $label Type label of the attribute item
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Attribute\Item\Iface
    {
        return $this->set('attribute.label', $label);
    }
    /**
     * Returns the status (enabled/disabled) of the attribute item.
     *
     * @return int Returns the status of the item
     */
    public function get_status(): int
    {
        return (int) $this->get('attribute.status', 1);
    }
    /**
     * Sets the new status of the attribute item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('attribute.status', $status);
    }
    /**
     * Gets the position of the attribute item.
     *
     * @return integer Position of the attribute item
     */
    public function get_position(): int
    {
        return $this->get('attribute.position', 0);
    }
    /**
     * Sets the position of the attribute item
     *
     * @param int $pos Position of the attribute item
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function set_position(int $pos): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('attribute.position', $pos);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_status() > 0;
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'attribute.domain':
                    $item->set_domain($value);
                    break;
                case 'attribute.code':
                    $item->set_code($value);
                    break;
                case 'attribute.type':
                    $item->set_type($value);
                    break;
                case 'attribute.status':
                    $item->set_status((int) $value);
                    break;
                case 'attribute.position':
                    $item->set_position((int) $value);
                    break;
                case 'attribute.label':
                    $item->set_label($value);
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
        $list['attribute.domain'] = $this->get_domain();
        $list['attribute.type'] = $this->get_type();
        $list['attribute.code'] = $this->get_code();
        $list['attribute.label'] = $this->get_label();
        $list['attribute.status'] = $this->get_status();
        $list['attribute.position'] = $this->get_position();
        if ($private === true) {
            $list['attribute.key'] = $this->get_key();
        }
        return $list;
    }
}