<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider;

/**
 * Abstract model for coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class Base implements Iface, \Aimeos\Macro\Iface
{
    use \Aimeos\Macro\Macroable;
    private ?\Aimeos\M_Shop\Coupon\Provider\Iface $object = null;
    /**
     * Initializes the coupon model.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item to set
     * @param string $code Coupon code entered by the customer
     */
    public function __construct(private \Aimeos\M_Shop\Context_Iface $context, private \Aimeos\M_Shop\Coupon\Item\Iface $item, private string $code)
    {
    }
    /**
     * Returns the price the discount should be applied to
     *
     * The result depends on the configured restrictions and it must be less or
     * equal to the passed price.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return \Aimeos\MShop\Price\Item\Iface New price that should be used
     */
    public function calc_price(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Price\Item\Iface
    {
        $price = \Aimeos\M_Shop::create($this->context, 'price')->create();
        foreach ($order->get_products() as $product) {
            $price = $price->add_item($product->get_price(), $product->get_quantity());
        }
        return $price;
    }
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
     */
    public function check_config_be(array $attributes): array
    {
        return [];
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return [];
    }
    /**
     * Tests if a valid coupon code should be granted
     *
     * The result depends on the configured restrictions and it doesn't test
     * again if the coupon or the code itself are still available.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return bool True of coupon can be granted, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $order): bool
    {
        return true;
    }
    /**
     * Injects the reference of the outmost object
     *
     * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outmost provider or decorator
     * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon object for chaining method calls
     */
    public function set_object(\Aimeos\M_Shop\Coupon\Provider\Iface $object): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        $this->object = $object;
        return $this;
    }
    /**
     * Checks required fields and the types of the given data map
     *
     * @param array $criteria Multi-dimensional associative list of criteria configuration
     * @param array $map Values to check agains the criteria
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
     */
    protected function check_config(array $criteria, array $map): array
    {
        $helper = new \Aimeos\M_Shop\Common\Helper\Config\Standard($this->get_config_items($criteria));
        return $helper->check($map);
    }
    /**
     * Returns the coupon code the provider is responsible for.
     *
     * @return string Coupon code
     */
    protected function get_code(): string
    {
        return $this->code;
    }
    /**
     * Returns the criteria attribute items for the backend configuration
     *
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of criteria attribute items
     */
    protected function get_config_items(array $config_list): array
    {
        $list = [];
        foreach ($config_list as $key => $config) {
            $list[$key] = new \Aimeos\Base\Criteria\Attribute\Standard($config);
        }
        return $list;
    }
    /**
     * Returns the configuration value from the service item specified by its key.
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if configuration key isn't available
     * @return mixed Value from service item configuration
     */
    protected function get_config_value(string $key, $default = null)
    {
        return $this->item->get_config_value($key, $default);
    }
    /**
     * Returns the stored context object.
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    protected function context(): \Aimeos\M_Shop\Context_Iface
    {
        return $this->context;
    }
    /**
     * Returns the stored coupon item.
     *
     * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item
     */
    protected function get_item(): \Aimeos\M_Shop\Coupon\Item\Iface
    {
        return $this->item;
    }
    /**
     * Returns the outmost decorator of the decorator stack
     *
     * @return \Aimeos\MShop\Coupon\Provider\Iface Outmost decorator object
     */
    protected function object(): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        if ($this->object !== null) {
            return $this->object;
        }
        return $this;
    }
    /**
     * Creates an order product for the given product code
     *
     * @param string $prodcode Unique product code
     * @param float $quantity Number of products
     * @param string $stocktype Unique stock type code for the order product
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order product
     */
    protected function create_product(string $prodcode, float $quantity = 1, string $stocktype = 'default'): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        $product_manager = \Aimeos\M_Shop::create($this->context, 'product');
        $product = $product_manager->find($prodcode, ['text', 'media', 'price']);
        $price_manager = \Aimeos\M_Shop::create($this->context, 'price');
        $prices = $product->get_ref_items('price', 'default', 'default');
        if (!$prices->is_empty()) {
            $price = $price_manager->get_lowest_price($prices, $quantity);
        } else {
            $price = $price_manager->create();
        }
        return \Aimeos\M_Shop::create($this->context, 'order/product')->create()->copy_from($product)->set_quantity($quantity)->set_stock_type($stocktype)->set_price($price)->set_flags(\Aimeos\M_Shop\Order\Item\Product\Base::FLAG_IMMUTABLE);
    }
    /**
     * Creates the order products for monetary rebates.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @param string $prodcode Unique product code
     * @param float $rebate Rebate amount that should be granted, will contain the remaining rebate if not fully used
     * @param float $quantity Number of products in basket
     * @param string $stockType Unique code of the stock type the product is from
     * @return \Aimeos\MShop\Order\Item\Product\Iface[] Order products with monetary rebates
     */
    protected function create_rebate_products(\Aimeos\M_Shop\Order\Item\Iface $order, string $prodcode, float &$rebate, float $quantity = 1, string $stock_type = 'default'): array
    {
        $order_products = [];
        if (($prices = $this->get_price_by_tax_rate($order))->is_empty()) {
            $prices = ['0.00' => \Aimeos\M_Shop::create($this->context(), 'price')->create()];
        }
        foreach ($prices as $taxrate => $price) {
            if ($rebate < 0.01) {
                break;
            }
            if (($amount = $price->get_value() + $price->get_costs()) < 0.01) {
                continue;
            }
            if ($price->get_value() <= $rebate) {
                $rebate -= $value = $price->get_value();
            } else {
                $value = $rebate;
                $rebate = 0;
            }
            if ($price->get_costs() <= $rebate) {
                $rebate -= $costs = $price->get_costs();
            } else {
                $costs = $rebate;
                $rebate = 0;
            }
            $order_product = $this->create_product($prodcode, $quantity, $stock_type);
            $price = $order_product->get_price()->set_tax_rate($taxrate)->set_value(-$value)->set_costs(-$costs)->set_rebate($value + $costs);
            $order_products[] = $order_product->set_price($price);
        }
        usort($order_products, fn($a, $b): int => $a->get_price()->get_value() <=> $b->get_price()->get_value());
        return $order_products;
    }
    /**
     * Returns a list of tax rates and their price items for the given basket.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket containing the products, services, etc.
     * @return \Aimeos\Map Associative list of tax rates as key and price items implementing \Aimeos\MShop\Price\Item\Iface
     */
    protected function get_price_by_tax_rate(\Aimeos\M_Shop\Order\Item\Iface $basket): \Aimeos\Map
    {
        $prices = map();
        $manager = \Aimeos\M_Shop::create($this->context(), 'price');
        $newprice = $manager->create();
        $map = $basket->get_coupons();
        $products = $map[$this->get_code()] ?? [];
        foreach ($basket->get_products() as $item) {
            if (!in_array($item, $products, true)) {
                $price = $item->get_price();
                $rate = $price->get_tax_rate();
                $prices[$rate] = $prices->get($rate, clone $newprice)->add_item($price, $item->get_quantity());
            }
        }
        foreach ($basket->get_services() as $services) {
            foreach ($services as $item) {
                $price = $item->get_price();
                $rate = $price->get_tax_rate();
                $prices[$rate] = $prices->get($rate, clone $newprice)->add_item($price);
            }
        }
        return $prices->krsort();
    }
}