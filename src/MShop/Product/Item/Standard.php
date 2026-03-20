<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Product
 */
namespace Aimeos\M_Shop\Product\Item;

use Aimeos\M_Shop\Common\Item\Config;
use Aimeos\M_Shop\Common\Item\Lists_Ref;
use Aimeos\M_Shop\Common\Item\Property_Ref;
use Aimeos\M_Shop\Common\Item\Type_Ref;
/**
 * Default impelementation of a product item.
 *
 * @package MShop
 * @subpackage Product
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Product\Item\Iface
{
    use Stock, Config\Traits, Lists_Ref\Traits, Property_Ref\Traits, Type_Ref\Traits {
        Property_Ref\Traits::__clone as __cloneProperty;
        Lists_Ref\Traits::__clone as __cloneList;
        Lists_Ref\Traits::getName as getNameList;
        Stock::__clone as __cloneStock;
    }
    /**
     * Initializes the item object.
     *
     * @param string $prefix Domain specific prefix string
     * @param array $values Parameter for initializing the basic properties
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values);
        $this->init_property_items($values['.propitems'] ?? []);
        $this->init_list_items($values['.listitems'] ?? []);
        $this->init_stock_items($values['.stock'] ?? []);
    }
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        $this->__clone_list();
        $this->__clone_stock();
        $this->__clone_property();
    }
    /**
     * Returns the parent product items referencing the product
     *
     * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Product\Item\Iface
     */
    public function get_parent_items(): \Aimeos\Map
    {
        return map($this->get('.parent'));
    }
    /**
     * Returns the supplier items referencing the product
     *
     * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Supplier\Item\Iface
     */
    public function get_site_item(): ?\Aimeos\M_Shop\Locale\Item\Site\Iface
    {
        return $this->get('.locale/site');
    }
    /**
     * Returns the status of the product item.
     *
     * @return int Status of the product item
     */
    public function get_status(): int
    {
        return $this->get('product.status', 1);
    }
    /**
     * Sets the new status of the product item.
     *
     * @param int $status New status of the product item
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('product.status', $status);
    }
    /**
     * Returns the code of the product item.
     *
     * @return string Code of the product item
     */
    public function get_code(): string
    {
        return $this->get('product.code', '');
    }
    /**
     * Sets the new code of the product item.
     *
     * @param string $code New code of product item
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.code', \Aimeos\Utils::code($code));
    }
    /**
     * Returns the data set name assigned to the product item.
     *
     * @return string Data set name
     */
    public function get_dataset(): string
    {
        return $this->get('product.dataset', '');
    }
    /**
     * Sets a new data set name assignd to the product item.
     *
     * @param string $name New data set name
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_dataset(?string $name): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.dataset', \Aimeos\Utils::code((string) $name));
    }
    /**
     * Returns the label of the product item.
     *
     * @return string Label of the product item
     */
    public function get_label(): string
    {
        return $this->get('product.label', '');
    }
    /**
     * Sets a new label of the product item.
     *
     * @param string $label New label of the product item
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.label', $label);
    }
    /**
     * Returns the localized text type of the item or the internal label if no name is available.
     *
     * @param string $type Text type to be returned
     * @param string|null $langId Two letter ISO Language code of the text
     * @return string Specified text type or label of the item
     */
    public function get_name(string $type = 'name', ?string $lang_id = null): string
    {
        $name = $this->get_name_list($type, $lang_id);
        if ($type === 'url' && $name === $this->get_label()) {
            return $this->get_url();
        }
        return $name;
    }
    /**
     * Returns the URL segment for the product item.
     *
     * @return string URL segment of the product item
     */
    public function get_url(): string
    {
        return (string) $this->get('product.url') ?: \Aimeos\Base\Str::slug($this->get_label());
    }
    /**
     * Sets a new URL segment for the product.
     *
     * @param string|null $url New URL segment of the product item
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_url(?string $url): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.url', \Aimeos\Base\Str::slug($url));
    }
    /**
     * Returns the starting point of time, in which the product is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_start(): ?string
    {
        $value = $this->get('product.datestart');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new starting point of time, in which the product is available.
     *
     * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_date_start(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('product.datestart', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the ending point of time, in which the product is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_end(): ?string
    {
        $value = $this->get('product.dateend');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new ending point of time, in which the product is available.
     *
     * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_date_end(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('product.dateend', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the rating of the item
     *
     * @return string Decimal value of the item rating
     */
    public function get_rating(): string
    {
        return (string) $this->get('product.rating', 0);
    }
    /**
     * Returns the total number of ratings for the item
     *
     * @return int Total number of ratings for the item
     */
    public function get_ratings(): int
    {
        return (int) $this->get('product.ratings', 0);
    }
    /**
     * Returns the quantity scale of the product item.
     *
     * @return float Quantity scale
     */
    public function get_scale(): float
    {
        return (float) $this->get('product.scale', 1) ?: 1;
    }
    /**
     * Sets a new quantity scale of the product item.
     *
     * @param float $value New quantity scale
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_scale(float $value): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.scale', $value > 0 ? $value : 1);
    }
    /**
     * Returns the URL target specific for that product
     *
     * @return string URL target specific for that product
     */
    public function get_target(): string
    {
        return $this->get('product.target', '');
    }
    /**
     * Sets a new URL target specific for that product
     *
     * @param string $value New URL target specific for that product
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_target(?string $value): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.target', (string) $value);
    }
    /**
     * Returns the create date of the item
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_time_created(): ?string
    {
        return $this->get('product.ctime');
    }
    /**
     * Sets the create date of the item
     *
     * @param string|null $value ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_time_created(?string $value): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.ctime', \Aimeos\Utils::datetime($value));
    }
    /**
     * Returns the type of the product item.
     * Overwritten for different default value.
     *
     * @return string Type of the product item
     */
    public function get_type(): string
    {
        return $this->get('product.type', 'default');
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        $date = $this->get('.date') ?: date('Y-m-d H:i:s');
        return parent::is_available() && $this->get_status() > 0 && ($this->get_date_end() === null || $this->get_date_end() > $date) && ($this->get_date_start() === null || $this->get_date_start() < $date || $this->get_type() === 'event');
    }
    /**
     * Returns the flag if stock is available for that product.
     *
     * @return int "1" if product is in stock, "0" if product is out of stock
     */
    public function in_stock(): int
    {
        return (int) $this->get('product.instock', 0);
    }
    /**
     * Sets the flag if stock is available for that product.
     *
     * @param int $value "1" if product is in stock, "0" if product is out of stock
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_in_stock(int $value): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.instock', $value);
    }
    /**
     * Returns the boost factor for that product.
     *
     * @return float Boost factor
     */
    public function boost(): float
    {
        return (float) $this->get('product.boost', 1);
    }
    /**
     * Sets the boost factor for that product.
     *
     * @param float $value Boost factor
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_boost(float $value): \Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->set('product.boost', $value);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'product.url':
                    $item->set_url($value);
                    break;
                case 'product.type':
                    $item->set_type($value);
                    break;
                case 'product.code':
                    $item->set_code($value);
                    break;
                case 'product.label':
                    $item->set_label($value);
                    break;
                case 'product.dataset':
                    $item->set_dataset($value);
                    break;
                case 'product.scale':
                    $item->set_scale((float) $value);
                    break;
                case 'product.status':
                    $item->set_status((int) $value);
                    break;
                case 'product.datestart':
                    $item->set_date_start($value);
                    break;
                case 'product.dateend':
                    $item->set_date_end($value);
                    break;
                case 'product.config':
                    $item->set_config($value);
                    break;
                case 'product.target':
                    $item->set_target($value);
                    break;
                case 'product.ctime':
                    $item->set_time_created($value);
                    break;
                case 'product.instock':
                    $item->set_in_stock((bool) $value);
                    break;
                case 'product.boost':
                    $item->set_boost((float) $value);
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
        $list['product.url'] = $this->get_url();
        $list['product.type'] = $this->get_type();
        $list['product.code'] = $this->get_code();
        $list['product.label'] = $this->get_label();
        $list['product.status'] = $this->get_status();
        $list['product.dataset'] = $this->get_dataset();
        $list['product.datestart'] = $this->get_date_start();
        $list['product.dateend'] = $this->get_date_end();
        $list['product.config'] = $this->get_config();
        $list['product.scale'] = $this->get_scale();
        $list['product.target'] = $this->get_target();
        $list['product.ctime'] = $this->get_time_created();
        $list['product.ratings'] = $this->get_ratings();
        $list['product.rating'] = $this->get_rating();
        $list['product.instock'] = $this->in_stock();
        $list['product.boost'] = $this->boost();
        return $list;
    }
}