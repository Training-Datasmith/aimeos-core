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
 * Default order product item implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends Base implements Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Clones internal objects of the order product item.
     */
    public function __clone()
    {
        $this->set('.attributes', map($this->get('.attributes', []))->clone());
        $this->set('.products', map($this->get('.products', []))->clone());
        $this->set('.price', clone $this->get('.price'));
        parent::__clone();
    }
    /**
     * Returns the associated parent product item
     *
     * @return \Aimeos\MShop\Product\Item\Iface|null Product item
     */
    public function get_parent_product_item(): ?\Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->get('.parentproduct');
    }
    /**
     * Returns the associated product item
     *
     * @return \Aimeos\MShop\Product\Item\Iface|null Product item
     */
    public function get_product_item(): ?\Aimeos\M_Shop\Product\Item\Iface
    {
        return $this->get('.product');
    }
    /**
     * Returns the associated supplier item
     *
     * @return \Aimeos\MShop\Supplier\Item\Iface|null Supplier item
     */
    public function get_supplier_item(): ?\Aimeos\M_Shop\Supplier\Item\Iface
    {
        return $this->get('.supplier');
    }
    /**
     * Returns the price item for the product.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
     */
    public function get_price(): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->get('.price');
    }
    /**
     * Sets the price item for the product.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_price(\Aimeos\M_Shop\Price\Item\Iface $price): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('.price', $price);
    }
    /**
     * Returns all of sub-product items
     *
     * @return \Aimeos\Map List of product items implementing \Aimeos\MShop\Order\Item\Product\Iface
     */
    public function get_products(): \Aimeos\Map
    {
        return $this->get('.products', map());
    }
    /**
     * Sets all sub-product items
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of product items
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_products(iterable $products): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        ($products = map($products))->implements(\Aimeos\M_Shop\Order\Item\Product\Iface::class, true);
        return $this->set('.products', $products);
    }
    /**
     * Returns the ID of the site the item is stored
     *
     * @return string Site ID (or null if not available)
     */
    public function get_site_id(): string
    {
        return $this->get('order.product.siteid', '');
    }
    /**
     * Sets the site ID of the item.
     *
     * @param string $value Unique site ID of the item
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.siteid', $value);
    }
    /**
     * Returns the base ID.
     *
     * @return string|null Base ID
     */
    public function get_parent_id(): ?string
    {
        return $this->get('order.product.parentid');
    }
    /**
     * Sets the base order ID the product belongs to.
     *
     * @param string|null $value New order base ID
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_parent_id(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.parentid', $value);
    }
    /**
     * Returns the order address ID the product should be shipped to
     *
     * @return string|null Order address ID
     */
    public function get_order_address_id(): ?string
    {
        return $this->get('order.product.orderaddressid');
    }
    /**
     * Sets the order address ID the product should be shipped to
     *
     * @param string|null $value Order address ID
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_order_address_id(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.orderaddressid', $value);
    }
    /**
     * Returns the parent ID of the ordered product if there is one.
     * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
     *
     * @return string|null Order product ID
     */
    public function get_order_product_id(): ?string
    {
        return $this->get('order.product.orderproductid');
    }
    /**
     * Sets the parent ID of the ordered product.
     * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
     *
     * @param string|null $value Order product ID
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_order_product_id(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.orderproductid', $value);
    }
    /**
     * Returns the vendor.
     *
     * @return string Vendor name
     */
    public function get_vendor(): string
    {
        return $this->get('order.product.vendor', '');
    }
    /**
     * Sets the vendor.
     *
     * @param string|null $value Vendor name
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_vendor(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.vendor', (string) $value);
    }
    /**
     * Returns the product ID the customer has selected.
     *
     * @return string Original product ID
     */
    public function get_product_id(): string
    {
        return $this->get('order.product.productid', '');
    }
    /**
     * Sets the ID of a product the customer has selected.
     *
     * @param string|null $id Product Code ID
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_product_id(?string $id): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.productid', (string) $id);
    }
    /**
     * Returns the product ID of the parent product.
     *
     * @return string Product ID of the parent product
     */
    public function get_parent_product_id(): string
    {
        return $this->get('order.product.parentproductid', '');
    }
    /**
     * Sets the ID of the parent product the customer has selected.
     *
     * @param string|null $id Product ID of the parent product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_parent_product_id(?string $id): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.parentproductid', (string) $id);
    }
    /**
     * Returns the product code the customer has selected.
     *
     * @return string Product code
     */
    public function get_product_code(): string
    {
        return $this->get('order.product.prodcode', '');
    }
    /**
     * Sets the code of a product the customer has selected.
     *
     * @param string $code Product code
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_product_code(string $code): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.prodcode', \Aimeos\Utils::code($code));
    }
    /**
     * Returns the code of the stock type the product should be retrieved from.
     *
     * @return string Stock type
     */
    public function get_stock_type(): string
    {
        return $this->get('order.product.stocktype', 'default');
    }
    /**
     * Sets the code of the stock type the product should be retrieved from.
     *
     * @param string|null $code Stock type
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_stock_type(?string $code): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.stocktype', \Aimeos\Utils::code((string) $code));
    }
    /**
     * Returns the localized name of the product.
     *
     * @param string|null $type Type the name is used for, e.g. "url"
     * @return string Returns the localized name of the product
     */
    public function get_name(?string $type = null): string
    {
        if ($type === 'url') {
            return \Aimeos\Base\Str::slug($this->get('order.product.name', '') ?: $this->get_product_code());
        }
        return $this->get('order.product.name', '');
    }
    /**
     * Sets the localized name of the product.
     *
     * @param string|null $value Localized name of the product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_name(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.name', (string) $value);
    }
    /**
     * Returns the localized description of the product.
     *
     * @return string Returns the localized description of the product
     */
    public function get_description(): string
    {
        return $this->get('order.product.description', '');
    }
    /**
     * Sets the localized description of the product.
     *
     * @param string|null $value Localized description of the product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_description(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.description', (string) $value);
    }
    /**
     * Returns the location of the media.
     *
     * @return string Location of the media
     */
    public function get_media_url(): string
    {
        return $this->get('order.product.mediaurl', '');
    }
    /**
     * Sets the media url of the product the customer has added.
     *
     * @param string|null $value Location of the media/picture
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_media_url(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.mediaurl', (string) $value);
    }
    /**
     * Returns the URL target specific for that product
     *
     * @return string URL target specific for that product
     */
    public function get_target(): string
    {
        return $this->get('order.product.target', '');
    }
    /**
     * Sets the URL target specific for that product
     *
     * @param string|null $value New URL target specific for that product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_target(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.target', (string) $value);
    }
    /**
     * Returns the expected delivery time frame
     *
     * @return string Expected delivery time frame
     */
    public function get_timeframe(): string
    {
        return $this->get('order.product.timeframe', '');
    }
    /**
     * Sets the expected delivery time frame
     *
     * @param string|null $timeframe Expected delivery time frame
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_timeframe(?string $timeframe): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.timeframe', (string) $timeframe);
    }
    /**
     * Returns the amount of products the customer has added.
     *
     * @return float Amount of products
     */
    public function get_quantity(): float
    {
        return (float) $this->get('order.product.quantity', 1);
    }
    /**
     * Sets the amount of products the customer has added.
     *
     * @param float $quantity Amount of products
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_quantity(float $quantity): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        if ($quantity <= 0 || $quantity > 2147483647) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Quantity must be greater than 0 and must not exceed 2147483647'));
        }
        return $this->set('order.product.quantity', $quantity);
    }
    /**
     * Returns the number of packages not yet delivered to the customer.
     *
     * @return float Amount of product packages
     */
    public function get_quantity_open(): float
    {
        return (float) $this->get('order.product.qtyopen', $this->get_quantity());
    }
    /**
     * Sets the number of product packages not yet delivered to the customer.
     *
     * @param float $quantity Amount of product packages
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_quantity_open(float $quantity): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        if ($quantity < 0 || $quantity > $this->get_quantity()) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Quantity must be 0 or greater and must not exceed ordered quantity'));
        }
        return $this->set('order.product.qtyopen', $quantity);
    }
    /**
     * Returns the quantity scale of the product.
     *
     * @return float Minimum quantity value
     */
    public function get_scale(): float
    {
        return (float) $this->get('order.product.scale', 1);
    }
    /**
     * Sets the quantity scale of the product.
     *
     * @param float $quantity Minimum quantity value
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_scale(float $quantity): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        if ($quantity <= 0) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Quantity scale must be greater than 0'));
        }
        return $this->set('order.product.scale', $quantity);
    }
    /**
     * 	Returns the flags for the product item.
     *
     * @return int Flags, e.g. for immutable products
     */
    public function get_flags(): int
    {
        return $this->get('order.product.flags', \Aimeos\M_Shop\Order\Item\Product\Base::FLAG_NONE);
    }
    /**
     * Sets the new value for the product item flags.
     *
     * @param int $value Flags, e.g. for immutable products
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_flags(int $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.flags', $this->check_flags($value));
    }
    /**
     * Returns the position of the product in the order.
     *
     * @return int|null Product position in the order from 0-n
     */
    public function get_position(): ?int
    {
        return $this->get('order.product.position');
    }
    /**
     * Sets the position of the product within the list of ordered products.
     *
     * @param int|null $value Product position in the order from 0-n or null for resetting the position
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     * @throws \Aimeos\MShop\Order\Exception If the position is invalid
     */
    public function set_position(?int $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        if ($value < 0) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Order product position "%1$s" must be greater than 0', $value));
        }
        return $this->set('order.product.position', $value);
    }
    /**
     * Returns the current delivery status of the order product item.
     *
     * The returned status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
     *
     * @return int Delivery status of the product
     */
    public function get_status_delivery(): int
    {
        return $this->get('order.product.statusdelivery', -1);
    }
    /**
     * Sets the new delivery status of the order product item.
     *
     * Possible status values are the STAT_* constants from the \Aimeos\MShop\Order\Item\Base class
     *
     * @param int $value New delivery status of the product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_status_delivery(int $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.statusdelivery', $value);
    }
    /**
     * Returns the current payment status of the order product item.
     *
     * The returned status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
     *
     * @return int Payment status of the product
     */
    public function get_status_payment(): int
    {
        return $this->get('order.product.statuspayment', -1);
    }
    /**
     * Sets the new payment status of the order product item.
     *
     * Possible status values are the PAY_* constants from the \Aimeos\MShop\Order\Item\Base class
     *
     * @param int $value New payment status of the product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_status_payment(int $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.statuspayment', $value);
    }
    /**
     * Returns the notes for the ordered product.
     *
     * @return string Notes for the ordered product
     */
    public function get_notes(): string
    {
        return $this->get('order.product.notes', '');
    }
    /**
     * Sets the notes for the ordered product.
     *
     * @param string|null $value Notes for the ordered product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
     */
    public function set_notes(?string $value): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        return $this->set('order.product.notes', (string) $value);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order product item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $price = $this->get_price();
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.product.siteid':
                    !$private ?: $item->set_site_id($value);
                    break;
                case 'order.product.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'order.product.orderproductid':
                    !$private ?: $item->set_order_product_id($value);
                    break;
                case 'order.product.orderaddressid':
                    !$private ?: $item->set_order_address_id($value);
                    break;
                case 'order.product.position':
                    !$private ?: $item->set_position((int) $value);
                    break;
                case 'order.product.flags':
                    !$private ?: $item->set_flags((int) $value);
                    break;
                case 'order.product.target':
                    !$private ?: $item->set_target($value);
                    break;
                case 'order.product.parentproductid':
                    $item->set_parent_product_id($value);
                    break;
                case 'order.product.productid':
                    $item->set_product_id($value);
                    break;
                case 'order.product.prodcode':
                    $item->set_product_code($value);
                    break;
                case 'order.product.vendor':
                    $item->set_vendor($value);
                    break;
                case 'order.product.stocktype':
                    $item->set_stock_type($value);
                    break;
                case 'order.product.type':
                    $item->set_type($value);
                    break;
                case 'order.product.currencyid':
                    $price = $price->set_currency_id($value);
                    break;
                case 'order.product.price':
                    $price = $price->set_value($value);
                    break;
                case 'order.product.costs':
                    $price = $price->set_costs($value);
                    break;
                case 'order.product.rebate':
                    $price = $price->set_rebate($value);
                    break;
                case 'order.product.taxrates':
                    $price = $price->set_tax_rates($value);
                    break;
                case 'order.product.taxvalue':
                    $price = $price->set_tax_value($value);
                    break;
                case 'order.product.taxflag':
                    $price = $price->set_tax_flag($value);
                    break;
                case 'order.product.name':
                    $item->set_name($value);
                    break;
                case 'order.product.description':
                    $item->set_description($value);
                    break;
                case 'order.product.mediaurl':
                    $item->set_media_url($value);
                    break;
                case 'order.product.timeframe':
                    $item->set_time_frame($value);
                    break;
                case 'order.product.scale':
                    $item->set_scale((float) $value);
                    break;
                case 'order.product.quantity':
                    $item->set_quantity((float) $value);
                    break;
                case 'order.product.qtyopen':
                    $item->set_quantity_open((float) $value);
                    break;
                case 'order.product.notes':
                    $item->set_notes((string) $value);
                    break;
                case 'order.product.statusdelivery':
                    $item->set_status_delivery((int) $value);
                    break;
                case 'order.product.statuspayment':
                    $item->set_status_payment((int) $value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns the item values as associative list.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $price = $this->get_price();
        $list = parent::to_array($private);
        $list['order.product.type'] = $this->get_type();
        $list['order.product.stocktype'] = $this->get_stock_type();
        $list['order.product.prodcode'] = $this->get_product_code();
        $list['order.product.productid'] = $this->get_product_id();
        $list['order.product.parentproductid'] = $this->get_parent_product_id();
        $list['order.product.vendor'] = $this->get_vendor();
        $list['order.product.scale'] = $this->get_scale();
        $list['order.product.quantity'] = $this->get_quantity();
        $list['order.product.qtyopen'] = $this->get_quantity_open();
        $list['order.product.currencyid'] = $price->get_currency_id();
        $list['order.product.price'] = $price->get_value();
        $list['order.product.costs'] = $price->get_costs();
        $list['order.product.rebate'] = $price->get_rebate();
        $list['order.product.taxrates'] = $price->get_tax_rates();
        $list['order.product.taxvalue'] = $price->get_tax_value();
        $list['order.product.taxflag'] = $price->get_tax_flag();
        $list['order.product.name'] = $this->get_name();
        $list['order.product.description'] = $this->get_description();
        $list['order.product.mediaurl'] = $this->get_media_url();
        $list['order.product.timeframe'] = $this->get_time_frame();
        $list['order.product.position'] = $this->get_position();
        $list['order.product.notes'] = $this->get_notes();
        $list['order.product.statuspayment'] = $this->get_status_payment();
        $list['order.product.statusdelivery'] = $this->get_status_delivery();
        if ($private === true) {
            $list['order.product.parentid'] = $this->get_parent_id();
            $list['order.product.orderproductid'] = $this->get_order_product_id();
            $list['order.product.orderaddressid'] = $this->get_order_address_id();
            $list['order.product.target'] = $this->get_target();
            $list['order.product.flags'] = $this->get_flags();
        }
        return $list;
    }
    /**
     * Compares the properties of the given order product item with its own ones.
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface $item Order product item
     * @return bool True if the item properties are equal, false if not
     * @since 2015.10
     */
    public function compare(\Aimeos\M_Shop\Order\Item\Product\Iface $item): bool
    {
        if ($this->get_flags() === $item->get_flags() && $this->get_name() === $item->get_name() && $this->get_site_id() === $item->get_site_id() && $this->get_stock_type() === $item->get_stock_type() && $this->get_product_code() === $item->get_product_code() && $this->get_order_address_id() === $item->get_order_address_id()) {
            return true;
        }
        return false;
    }
    /**
     * Copys all data from a given product item.
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product item to copy from
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order product item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Product\Item\Iface $product): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        $values = $product->to_array();
        $this->from_array($values);
        $this->set_site_id($product->get_site_id());
        $this->set_product_code($product->get_code());
        $this->set_product_id($product->get_id());
        $this->set_type($product->get_type());
        $this->set_scale($product->get_scale());
        $this->set_target($product->get_target());
        $this->set_name($product->get_name());
        if ($item = $product->get_ref_items('text', 'basket', 'default')->first()) {
            $this->set_description($item->get_content());
        }
        if ($item = $product->get_ref_items('media', 'default', 'default')->first()) {
            $this->set_media_url($item->get_preview());
        }
        if ($item = $product->get_site_item()) {
            $this->set_vendor($item->get_label());
        }
        if (self::macro('copyFrom')) {
            $this->call('copyFrom', $product);
        }
        return $this->set_modified();
    }
}