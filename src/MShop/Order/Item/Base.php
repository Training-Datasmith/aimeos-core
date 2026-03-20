<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item;

/**
 * Base order item class with common constants and methods.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Order\Item\Iface, \Aimeos\Macro\Iface, \ArrayAccess, \JsonSerializable
{
    use \Aimeos\Macro\Macroable;
    use Publisher;
    /**
     * Unfinished delivery.
     * This is the default status after creating an order and this status
     * should be also used as long as technical errors occurs.
     */
    public const STAT_UNFINISHED = -1;
    /**
     * Delivery was deleted.
     * The delivery of the order was deleted manually.
     */
    public const STAT_DELETED = 0;
    /**
     * Delivery is pending.
     * The order is not yet in the fulfillment process until further actions
     * are taken.
     */
    public const STAT_PENDING = 1;
    /**
     * Fulfillment in progress.
     * The delivery of the order is in the (internal) fulfillment process and
     * will be ready soon.
     */
    public const STAT_PROGRESS = 2;
    /**
     * Parcel is dispatched.
     * The parcel was given to the logistic partner for delivery to the
     * customer.
     */
    public const STAT_DISPATCHED = 3;
    /**
     * Parcel was delivered.
     * The logistic partner delivered the parcel and the customer received it.
     */
    public const STAT_DELIVERED = 4;
    /**
     * Parcel is lost.
     * The parcel is lost during delivery by the logistic partner and haven't
     * reached the customer nor it's returned to the merchant.
     */
    public const STAT_LOST = 5;
    /**
     * Parcel was refused.
     * The delivery of the parcel failed because the customer has refused to
     * accept it or the address was invalid.
     */
    public const STAT_REFUSED = 6;
    /**
     * Parcel was returned.
     * The parcel was sent back by the customer.
     */
    public const STAT_RETURNED = 7;
    /**
     * Unfinished payment.
     * This is the default status after creating an order and this status
     * should be also used as long as technical errors occurs.
     */
    public const PAY_UNFINISHED = -1;
    /**
     * Payment was deleted.
     * The payment for the order was deleted manually.
     */
    public const PAY_DELETED = 0;
    /**
     * Payment was canceled.
     * The customer canceled the payment process.
     */
    public const PAY_CANCELED = 1;
    /**
     * Payment was refused.
     * The customer didn't enter valid payment details.
     */
    public const PAY_REFUSED = 2;
    /**
     * Payment was refund.
     * The payment was OK but refund and the customer got his money back.
     */
    public const PAY_REFUND = 3;
    /**
     * Payment is pending.
     * The payment is not yet done until further actions are taken.
     */
    public const PAY_PENDING = 4;
    /**
     * Payment is authorized.
     * The customer authorized the merchant to invoice the amount but the money
     * is not yet received. This is used for all post-paid orders.
     */
    public const PAY_AUTHORIZED = 5;
    /**
     * Payment is received.
     * The merchant received the money from the customer.
     */
    public const PAY_RECEIVED = 6;
    /**
     * Payment is transferred.
     * The vendor received the money from the platform.
     */
    public const PAY_TRANSFERRED = 7;
    // protected is a workaround for serialize problem
    protected ?\Aimeos\M_Shop\Customer\Item\Iface $customer;
    protected \Aimeos\M_Shop\Locale\Item\Iface $locale;
    protected \Aimeos\M_Shop\Price\Item\Iface $price;
    protected array $coupons = [];
    protected array $products = [];
    protected array $services = [];
    protected array $statuses = [];
    protected array $addresses = [];
    /**
     * Initializes the order object
     *
     * @param string $prefix Prefix for the keys in the associative array
     * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
     */
    public function __construct(string $prefix, array $values = [])
    {
        $this->customer = $values['.customer'] ?? null;
        $this->locale = $values['.locale'];
        $this->price = $values['.price'];
        $products = $values['.products'] ?? [];
        foreach ($values['.coupons'] ?? [] as $coupon) {
            if (!isset($this->coupons[$coupon->get_code()])) {
                $this->coupons[$coupon->get_code()] = [];
            }
            if (isset($products[$coupon->get_product_id()])) {
                $this->coupons[$coupon->get_code()][] = $products[$coupon->get_product_id()];
            }
        }
        foreach ($values['.products'] ?? [] as $product) {
            $this->products[$product->get_position()] = $product;
        }
        foreach ($values['.addresses'] ?? [] as $address) {
            $this->addresses[$address->get_type()][] = $address;
        }
        foreach ($values['.services'] ?? [] as $service) {
            $this->services[$service->get_type()][] = $service;
        }
        foreach ($values['.statuses'] ?? [] as $status) {
            $this->statuses[$status->get_type()][$status->get_value()] = $status;
        }
        unset($values['.customer'], $values['.locale'], $values['.price'], $values['.statuses']);
        unset($values['.products'], $values['.coupons'], $values['.addresses'], $values['.services']);
        parent::__construct($prefix, $values);
    }
    /**
     * Clones internal objects of the order item.
     */
    public function __clone()
    {
        parent::__clone();
        $this->price = clone $this->price;
        $this->locale = clone $this->locale;
    }
    /**
     * Specifies the data which should be serialized to JSON by json_encode().
     *
     * @return array<string,mixed> Data to serialize to JSON
     */
    #[\Return_Type_Will_Change]
    public function jsonSerialize()
    {
        return parent::jsonSerialize() + ['addresses' => $this->addresses, 'products' => $this->products, 'services' => $this->services, 'coupons' => $this->coupons, 'customer' => $this->customer, 'locale' => $this->locale, 'price' => $this->price];
    }
    /**
     * Prepares the object for serialization.
     *
     * @return array List of properties that should be serialized
     */
    public function __sleep(): array
    {
        /*
         * Workaround because database connections can't be serialized
         * Listeners will be reattached on wakeup by the order base manager
         */
        $this->off();
        return array_keys(get_object_vars($this));
    }
    /**
     * Returns the ID of the items
     *
     * @return string ID of the item or null
     */
    public function __toString(): string
    {
        return (string) $this->get_id();
    }
    /**
     * Tests if all necessary items are available to create the order.
     *
     * @param array $what Type of data
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     * @throws \Aimeos\MShop\Order\Exception if there are no products in the basket
     */
    public function check(array $what = ['order/address', 'order/coupon', 'order/product', 'order/service']): \Aimeos\M_Shop\Order\Item\Iface
    {
        $this->notify('check.before', $what);
        if (in_array('order/product', $what) && count($this->get_products()) < 1) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Basket empty'));
        }
        $this->notify('check.after', $what);
        return $this;
    }
    /**
     * Notifies listeners before the basket becomes an order.
     *
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
     */
    public function finish(): \Aimeos\M_Shop\Order\Item\Iface
    {
        $this->notify('setOrder.before');
        return $this;
    }
    /**
     * Adds the address of the given type to the basket
     *
     * @param \Aimeos\MShop\Order\Item\Address\Iface $address Order address item for the given type
     * @param string $type Address type, usually "billing" or "delivery"
     * @param int|null $position Position of the address in the list
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function add_address(\Aimeos\M_Shop\Order\Item\Address\Iface $address, string $type, ?int $position = null): \Aimeos\M_Shop\Order\Item\Iface
    {
        $address = $this->notify('addAddress.before', $address);
        $address = clone $address;
        $address = $address->set_type($type);
        if ($position !== null) {
            $this->addresses[$type][$position] = $address;
        } else {
            $this->addresses[$type][] = $address;
        }
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('addAddress.after', $address);
        return $this;
    }
    /**
     * Deletes an order address from the basket
     *
     * @param string $type Address type defined in \Aimeos\MShop\Order\Item\Address\Base
     * @param int|null $position Position of the address in the list
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function delete_address(string $type, ?int $position = null): \Aimeos\M_Shop\Order\Item\Iface
    {
        if ($position === null && isset($this->addresses[$type]) || isset($this->addresses[$type][$position])) {
            $old = $this->addresses[$type][$position] ?? $this->addresses[$type];
            $old = $this->notify('deleteAddress.before', $old);
            if ($position !== null) {
                unset($this->addresses[$type][$position]);
            } else {
                unset($this->addresses[$type]);
            }
            $this->price->set_modified();
            $this->set_modified();
            $this->notify('deleteAddress.after', $old);
        }
        return $this;
    }
    /**
     * Returns the order address depending on the given type
     *
     * @param string $type Address type, usually "billing" or "delivery"
     * @param int|null $position Address position in list of addresses
     * @return \Aimeos\MShop\Order\Item\Address\Iface[]|\Aimeos\MShop\Order\Item\Address\Iface Order address item or list of
     */
    public function get_address(string $type, ?int $position = null)
    {
        if ($position !== null) {
            if (isset($this->addresses[$type][$position])) {
                return $this->addresses[$type][$position];
            }
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Address not available'));
        }
        return $this->addresses[$type] ?? [];
    }
    /**
     * Returns all addresses that are part of the basket
     *
     * @return \Aimeos\Map Associative list of address items implementing
     *  \Aimeos\MShop\Order\Item\Address\Iface with "billing" or "delivery" as key
     */
    public function get_addresses(): \Aimeos\Map
    {
        return map($this->addresses);
    }
    /**
     * Replaces all addresses in the current basket with the new ones
     *
     * @param \Aimeos\Map|array $map Associative list of order addresses as returned by getAddresses()
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function set_addresses(iterable $map): \Aimeos\M_Shop\Order\Item\Iface
    {
        $map = $this->notify('setAddresses.before', $map);
        foreach ($map as $type => $items) {
            $this->check_addresses($items, $type);
        }
        $old = $this->addresses;
        $this->addresses = is_map($map) ? $map->to_array() : $map;
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('setAddresses.after', $old);
        return $this;
    }
    /**
     * Adds a coupon code and the given product item to the basket
     *
     * @param string $code Coupon code
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function add_coupon(string $code): \Aimeos\M_Shop\Order\Item\Iface
    {
        if (!isset($this->coupons[$code])) {
            $code = $this->notify('addCoupon.before', $code);
            $this->coupons[$code] = [];
            $this->price->set_modified();
            $this->set_modified();
            $this->notify('addCoupon.after', $code);
        }
        return $this;
    }
    /**
     * Removes a coupon and the related product items from the basket
     *
     * @param string $code Coupon code
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function delete_coupon(string $code): \Aimeos\M_Shop\Order\Item\Iface
    {
        if (isset($this->coupons[$code])) {
            $old = [$code => $this->coupons[$code]];
            $old = $this->notify('deleteCoupon.before', $old);
            foreach ($this->coupons[$code] as $product) {
                if (($key = array_search($product, $this->products, true)) !== false) {
                    unset($this->products[$key]);
                }
            }
            unset($this->coupons[$code]);
            $this->price->set_modified();
            $this->set_modified();
            $this->notify('deleteCoupon.after', $old);
        }
        return $this;
    }
    /**
     * Returns the available coupon codes and the lists of affected product items
     *
     * @return \Aimeos\Map Associative array of codes and lists of product items
     *  implementing \Aimeos\MShop\Order\Product\Iface
     */
    public function get_coupons(): \Aimeos\Map
    {
        return map($this->coupons);
    }
    /**
     * Sets a coupon code and the given product items in the basket.
     *
     * @param string $code Coupon code
     * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of coupon products
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function set_coupon(string $code, iterable $products = []): \Aimeos\M_Shop\Order\Item\Iface
    {
        $new = $this->notify('setCoupon.before', [$code => $products]);
        $products = $this->check_products(map($new)->first([]));
        if (isset($this->coupons[$code])) {
            foreach ($this->coupons[$code] as $product) {
                if (($key = array_search($product, $this->products, true)) !== false) {
                    unset($this->products[$key]);
                }
            }
        }
        foreach ($products as $product) {
            $this->products[] = $product;
        }
        $old = isset($this->coupons[$code]) ? [$code => $this->coupons[$code]] : [];
        $this->coupons[$code] = is_map($products) ? $products->to_array() : $products;
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('setCoupon.after', $old);
        return $this;
    }
    /**
     * Replaces all coupons in the current basket with the new ones
     *
     * @param iterable $map Associative list of order coupons as returned by getCoupons()
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function set_coupons(iterable $map): \Aimeos\M_Shop\Order\Item\Iface
    {
        $map = $this->notify('setCoupons.before', $map);
        foreach ($map as $code => $products) {
            $map[$code] = $this->check_products($products);
        }
        foreach ($this->coupons as $code => $products) {
            foreach ($products as $product) {
                if (($key = array_search($product, $this->products, true)) !== false) {
                    unset($this->products[$key]);
                }
            }
        }
        foreach ($map as $products) {
            foreach ($products as $product) {
                $this->products[] = $product;
            }
        }
        $old = $this->coupons;
        $this->coupons = is_map($map) ? $map->to_array() : $map;
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('setCoupons.after', $old);
        return $this;
    }
    /**
     * Adds an order product item to the basket
     * If a similar item is found, only the quantity is increased.
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface $item Order product item to be added
     * @param int|null $position position of the new order product item
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function add_product(\Aimeos\M_Shop\Order\Item\Product\Iface $item, ?int $position = null): \Aimeos\M_Shop\Order\Item\Iface
    {
        $item = $this->notify('addProduct.before', $item);
        $this->check_products([$item]);
        if ($position !== null) {
            $this->products[$position] = $item;
        } elseif (($pos = $this->get_same_product($item, $this->products)) !== null) {
            $item = $this->products[$pos]->set_quantity($this->products[$pos]->get_quantity() + $item->get_quantity());
        } else {
            $this->products[] = $item;
        }
        ksort($this->products);
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('addProduct.after', $item);
        return $this;
    }
    /**
     * Deletes an order product item from the basket
     *
     * @param int $position Position of the order product item
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function delete_product(int $position): \Aimeos\M_Shop\Order\Item\Iface
    {
        if (isset($this->products[$position])) {
            $old = $this->products[$position];
            $old = $this->notify('deleteProduct.before', $old);
            unset($this->products[$position]);
            $this->price->set_modified();
            $this->set_modified();
            $this->notify('deleteProduct.after', $old);
        }
        return $this;
    }
    /**
     * Returns the product item of an basket specified by its key
     *
     * @param int $key Key returned by getProducts() identifying the requested product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Product item of an order
     */
    public function get_product(int $key): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        if (!isset($this->products[$key])) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Product not available'));
        }
        return $this->products[$key];
    }
    /**
     * Returns the product items that are or should be part of a basket
     *
     * @return \Aimeos\Map List of order product items implementing \Aimeos\MShop\Order\Item\Product\Iface
     */
    public function get_products(): \Aimeos\Map
    {
        return map($this->products);
    }
    /**
     * Replaces all products in the current basket with the new ones
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface[] $map Associative list of ordered products as returned by getProducts()
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function set_products(iterable $map): \Aimeos\M_Shop\Order\Item\Iface
    {
        $map = $this->notify('setProducts.before', $map);
        $this->check_products($map);
        $old = $this->products;
        $this->products = is_map($map) ? $map->to_array() : $map;
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('setProducts.after', $old);
        return $this;
    }
    /**
     * Adds an order service to the basket
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface $service Order service item for the given domain
     * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
     * @param int|null $position Position of the service in the list to overwrite
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function add_service(\Aimeos\M_Shop\Order\Item\Service\Iface $service, string $type, ?int $position = null): \Aimeos\M_Shop\Order\Item\Iface
    {
        $service = $this->notify('addService.before', $service);
        $this->check_price($service->get_price());
        $service = clone $service;
        $service = $service->set_type($type);
        if ($position !== null) {
            $this->services[$type][$position] = $service;
        } else {
            $this->services[$type][] = $service;
        }
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('addService.after', $service);
        return $this;
    }
    /**
     * Deletes an order service from the basket
     *
     * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
     * @param int|null $position Position of the service in the list to delete
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function delete_service(string $type, ?int $position = null): \Aimeos\M_Shop\Order\Item\Iface
    {
        if ($position === null && isset($this->services[$type]) || isset($this->services[$type][$position])) {
            $old = $this->services[$type][$position] ?? $this->services[$type];
            $old = $this->notify('deleteService.before', $old);
            if ($position !== null) {
                unset($this->services[$type][$position]);
            } else {
                unset($this->services[$type]);
            }
            $this->price->set_modified();
            $this->set_modified();
            $this->notify('deleteService.after', $old);
        }
        return $this;
    }
    /**
     * Returns the order services depending on the given type
     *
     * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
     * @param int|null $position Position of the service in the list to retrieve
     * @return \Aimeos\MShop\Order\Item\Service\Iface[]|\Aimeos\MShop\Order\Item\Service\Iface
     * 	Order service item or list of items for the requested type
     * @throws \Aimeos\MShop\Order\Exception If no service for the given type and position is found
     */
    public function get_service(string $type, ?int $position = null)
    {
        if ($position !== null) {
            if (isset($this->services[$type][$position])) {
                return $this->services[$type][$position];
            }
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Service not available'));
        }
        return $this->services[$type] ?? [];
    }
    /**
     * Returns all services that are part of the basket
     *
     * @return \Aimeos\Map Associative list of service types ("delivery" or "payment") as keys and list of
     *	service items implementing \Aimeos\MShop\Order\Service\Iface as values
     */
    public function get_services(): \Aimeos\Map
    {
        return map($this->services);
    }
    /**
     * Replaces all services in the current basket with the new ones
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface[] $map Associative list of order services as returned by getServices()
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for method chaining
     */
    public function set_services(iterable $map): \Aimeos\M_Shop\Order\Item\Iface
    {
        $map = $this->notify('setServices.before', $map);
        foreach ($map as $type => $services) {
            $map[$type] = $this->check_services($services, $type);
        }
        $old = $this->services;
        $this->services = is_map($map) ? $map->to_array() : $map;
        $this->price->set_modified();
        $this->set_modified();
        $this->notify('setServices.after', $old);
        return $this;
    }
    /**
     * Adds a status item to the order
     *
     * @param \Aimeos\MShop\Order\Item\Status\Iface $item Order status item
     * @return \Aimeos\MShop\Order\Item\Iface Order item for method chaining
     */
    public function add_status(\Aimeos\M_Shop\Order\Item\Status\Iface $item): \Aimeos\M_Shop\Order\Item\Iface
    {
        $type = $item->get_type();
        $value = $item->get_value();
        if (isset($this->statuses[$type][$value])) {
            $this->statuses[$type][$value] = $item->set_id($this->statuses[$type][$value]->get_id());
        } else {
            $this->statuses[$type][$value] = $item;
        }
        return $this;
    }
    /**
     * Returns the latest status item specified by its type and value
     *
     * @param string $type Status type
     * @param string $value Status value
     * @return \Aimeos\MShop\Order\Item\Status\Iface|null Status item of an order or null if not available
     */
    public function get_status(string $type, string $value): ?\Aimeos\M_Shop\Order\Item\Status\Iface
    {
        return $this->statuses[$type][$value] ?? null;
    }
    /**
     * Returns the status items
     *
     * @return \Aimeos\Map Associative list of status types as keys and list of
     *	status value/item pairs implementing \Aimeos\MShop\Order\Status\Iface as values
     */
    public function get_statuses(): \Aimeos\Map
    {
        return map($this->statuses);
    }
    /**
     * Returns the service costs
     *
     * @param string $type Service type like "delivery" or "payment"
     * @return float Service costs value
     */
    public function get_costs(string $type = 'delivery'): float
    {
        $costs = 0;
        foreach ($this->get_service($type) as $service) {
            $costs += $service->get_price()->get_costs();
        }
        if ($type === 'delivery') {
            foreach ($this->get_products() as $product) {
                $costs += $product->get_price()->get_costs() * $product->get_quantity();
            }
        }
        return $costs;
    }
    /**
     * Returns a price item with amounts calculated for the products, costs, etc.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate the customer has to pay
     */
    public function get_price(): \Aimeos\M_Shop\Price\Item\Iface
    {
        if ($this->price->is_modified()) {
            $price = $this->price->clear();
            foreach ($this->get_services() as $list) {
                foreach ($list as $service) {
                    $price = $price->add_item($service->get_price());
                }
            }
            foreach ($this->get_products() as $product) {
                $price = $price->add_item($product->get_price(), $product->get_quantity());
            }
            $this->price = $price->set_id('');
            // clear modified flag
        }
        return $this->price;
    }
    /**
     * Returns a list of tax names and values
     *
     * @return array Associative list of tax names as key and price items as value
     */
    public function get_taxes(): array
    {
        $taxes = [];
        foreach ($this->get_products() as $product) {
            $price = $product->get_price();
            foreach ($price->get_taxrates() as $name => $taxrate) {
                $price = (clone $price)->set_tax_rate($taxrate);
                if (isset($taxes[$name][$taxrate])) {
                    $taxes[$name][$taxrate]->add_item($price, $product->get_quantity());
                } else {
                    $taxes[$name][$taxrate] = $price->add_item($price, $product->get_quantity() - 1);
                }
            }
        }
        foreach ($this->get_services() as $services) {
            foreach ($services as $service) {
                $price = $service->get_price();
                foreach ($price->get_taxrates() as $name => $taxrate) {
                    $price = (clone $price)->set_tax_rate($taxrate);
                    if (isset($taxes[$name][$taxrate])) {
                        $taxes[$name][$taxrate]->add_item($price);
                    } else {
                        $taxes[$name][$taxrate] = $price;
                    }
                }
            }
        }
        return $taxes;
    }
    /**
     * Returns the locales for the basic order item.
     *
     * @return \Aimeos\MShop\Locale\Item\Iface Object containing information
     *  about site, language, country and currency
     */
    public function locale(): \Aimeos\M_Shop\Locale\Item\Iface
    {
        return $this->locale;
    }
    /**
     * Sets the locales for the basic order item.
     *
     * @param \Aimeos\MShop\Locale\Item\Iface $locale Object containing information
     *  about site, language, country and currency
     * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
     */
    public function set_locale(\Aimeos\M_Shop\Locale\Item\Iface $locale): \Aimeos\M_Shop\Order\Item\Iface
    {
        $this->notify('setLocale.before', $locale);
        $this->locale = clone $locale;
        $this->notify('setLocale.after', $locale);
        return $this->set_modified();
    }
    /**
     * Returns the item values as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $price = $this->get_price();
        $list = parent::to_array($private);
        $list['order.currencyid'] = $price->get_currency_id();
        $list['order.price'] = $price->get_value();
        $list['order.costs'] = $price->get_costs();
        $list['order.rebate'] = $price->get_rebate();
        $list['order.taxflag'] = $price->get_tax_flag();
        $list['order.taxvalue'] = $price->get_tax_value();
        return $list;
    }
    /**
     * Checks if the price uses the same currency as the price in the basket.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $item Price item
     */
    protected function check_price(\Aimeos\M_Shop\Price\Item\Iface $item)
    {
        $price = clone $this->get_price();
        $price->add_item($item);
    }
    /**
     * Checks if all order addresses are valid
     *
     * @param \Aimeos\MShop\Order\Item\Address\Iface[] $items Order address items
     * @param string $type Address type constant from \Aimeos\MShop\Order\Item\Address\Base
     * @return \Aimeos\MShop\Order\Item\Address\Iface[] List of checked items
     * @throws \Aimeos\MShop\Exception If one of the order addresses is invalid
     */
    protected function check_addresses(iterable $items, string $type): iterable
    {
        map($items)->implements(\Aimeos\M_Shop\Order\Item\Address\Iface::class, true);
        foreach ($items as $key => $item) {
            $items[$key] = $item->set_type($type);
        }
        return $items;
    }
    /**
     * Checks if all order products are valid
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface[] $items Order product items
     * @return \Aimeos\MShop\Order\Item\Product\Iface[] List of checked items
     * @throws \Aimeos\MShop\Exception If one of the order products is invalid
     */
    protected function check_products(iterable $items): \Aimeos\Map
    {
        map($items)->implements(\Aimeos\M_Shop\Order\Item\Product\Iface::class, true);
        foreach ($items as $item) {
            if ($item->get_product_code() === '') {
                throw new \Aimeos\M_Shop\Order\Exception(sprintf('Product does not contain the SKU code'));
            }
            $this->check_price($item->get_price());
        }
        return map($items);
    }
    /**
     * Checks if all order services are valid
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface[] $items Order service items
     * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
     * @return \Aimeos\MShop\Order\Item\Service\Iface[] List of checked items
     * @throws \Aimeos\MShop\Exception If one of the order services is invalid
     */
    protected function check_services(iterable $items, string $type): iterable
    {
        map($items)->implements(\Aimeos\M_Shop\Order\Item\Service\Iface::class, true);
        foreach ($items as $key => $item) {
            $this->check_price($item->get_price());
            $items[$key] = $item->set_type($type);
        }
        return $items;
    }
    /**
     * Tests if the given product is similar to an existing one.
     * Similarity is described by the equality of properties so the quantity of
     * the existing product can be updated.
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface $item Order product item
     * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of order product items to check against
     * @return int|null Positon of the same product in the product list of false if product is unique
     * @throws \Aimeos\MShop\Order\Exception If no similar item was found
     */
    protected function get_same_product(\Aimeos\M_Shop\Order\Item\Product\Iface $item, iterable $products): ?int
    {
        $map = [];
        $count = 0;
        foreach ($item->get_attribute_items() as $attribute_item) {
            $key = md5($attribute_item->get_code() . json_encode($attribute_item->get_value()));
            $map[$key] = $attribute_item;
            $count++;
        }
        foreach ($products as $position => $product) {
            if ($product->compare($item) === false) {
                continue;
            }
            $prod_attributes = $product->get_attribute_items();
            if (count($prod_attributes) !== $count) {
                continue;
            }
            foreach ($prod_attributes as $attribute) {
                $key = md5($attribute->get_code() . json_encode($attribute->get_value()));
                if (isset($map[$key]) === false || $map[$key]->get_quantity() != $attribute->get_quantity()) {
                    continue 2;
                    // jump to outer loop
                }
            }
            return $position;
        }
        return null;
    }
}