<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider;

/**
 * Voucher coupon model
 *
 * @package MShop
 * @subpackage Coupon
 */
class Voucher extends \Aimeos\M_Shop\Coupon\Provider\Factory\Base implements \Aimeos\M_Shop\Coupon\Provider\Iface, \Aimeos\M_Shop\Coupon\Provider\Factory\Iface
{
    private array $be_config = ['voucher.productcode' => ['code' => 'voucher.productcode', 'internalcode' => 'voucher.productcode', 'label' => 'Product code of the rebate product', 'default' => '', 'required' => true]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        return $this->check_config($this->be_config, $attributes);
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return $this->get_config_items($this->be_config);
    }
    /**
     * Updates the result of a coupon to the order base instance.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        $context = $this->context();
        if (($prodcode = $this->get_config_value('voucher.productcode')) === null) {
            $msg = $context->translate('mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"');
            $msg = sprintf($msg, $this->get_item()->get_provider(), 'voucher.productcode');
            throw new \Aimeos\M_Shop\Coupon\Exception($msg);
        }
        $manager = \Aimeos\M_Shop::create($this->context(), 'coupon/code');
        $order_product_id = $manager->find($this->get_code())->get_ref();
        $status = [\Aimeos\M_Shop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\M_Shop\Order\Item\Base::PAY_RECEIVED];
        $this->check_voucher($order_product_id, $status);
        $order_product = $this->get_order_product_item($order_product_id, $order->get_price()->get_currency_id());
        $value = $order_product->get_price()->get_value() + $order_product->get_price()->get_rebate();
        $used_rebate = $this->get_used_rebate($this->get_code());
        $rebate = $value - $used_rebate;
        if ($rebate <= 0) {
            $msg = $context->translate('mshop', 'No more credit available for voucher "%1$s"');
            throw new \Aimeos\M_Shop\Coupon\Exception(sprintf($msg, $this->get_code()));
        }
        $order_products = $this->create_rebate_products($order, $prodcode, $rebate);
        $order_products = $this->set_order_attribute_rebate($order_products, $rebate);
        $order->set_coupon($this->get_code(), $order_products);
        return $this;
    }
    /**
     * Checks if the voucher for the given order product ID is still available
     *
     * @param string $orderProductId Order product ID of the bought voucher
     * @param integer[] $status List of allowed payment status values
     * @throws \Aimeos\MShop\Coupon\Exception If voucher isn't available any more
     */
    protected function check_voucher(string $order_product_id, array $status)
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'order');
        $search = $manager->filter();
        $expr = [$search->compare('==', 'order.product.id', $order_product_id), $search->compare('==', 'order.statuspayment', $status)];
        $search->set_conditions($search->and($expr));
        if ($manager->search($search)->is_empty()) {
            $msg = $context->translate('mshop', 'No bought voucher for code "%1$s" available');
            throw new \Aimeos\M_Shop\Coupon\Exception(sprintf($msg, $this->get_code()));
        }
    }
    /**
     * Filters the order IDs and removes those order which aren't payed
     *
     * @param string[] $ids List of order IDs to check
     * @return string[] List of filtered order IDs
     */
    protected function filter_order_ids(array $ids): array
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'order');
        $search = $manager->filter()->add('order.id', '==', $ids)->add('order.statuspayment', '>=', \Aimeos\M_Shop\Order\Item\Base::PAY_PENDING);
        return $manager->search($search)->get_id()->all();
    }
    /**
     * Returns the ordered product item for the ID which is checked against the given currency
     *
     * @param string $orderProductId Unique ID of the ordered product
     * @param string $currencyId Three letter ISO currecy code
     * @return \Aimeos\MShop\Order\Item\Product\Iface Order product item
     * @throws \Aimeos\MShop\Coupon\Exception If there's a mismatch between the currency IDs (order product vs. given one)
     */
    protected function get_order_product_item(string $order_product_id, string $currency_id): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'order/product');
        $order_product = $manager->get($order_product_id);
        $currency = $order_product->get_price()->get_currency_id();
        if ($currency_id !== $currency) {
            $msg = $context->translate('mshop', 'Bought voucher is in currency "%1$s", basket uses "%2$s"');
            throw new \Aimeos\M_Shop\Coupon\Exception(sprintf($msg, $currency, $currency_id));
        }
        return $order_product;
    }
    /**
     * Returns the already used rebate for the given voucher code
     *
     * @param string $code Voucher code
     * @return float Already used rebate value
     */
    protected function get_used_rebate(string $code): float
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'order/coupon');
        $search = $manager->filter()->slice(0, 0x7fffffff);
        $search->set_conditions($search->compare('==', 'order.coupon.code', $code));
        $order_ids = $prod_ids = [];
        foreach ($manager->search($search) as $order_coupon_item) {
            $prod_ids[] = $order_coupon_item->get_product_id();
            $order_ids[] = $order_coupon_item->get_parent_id();
        }
        $order_ids = $this->filter_order_ids($order_ids);
        $manager = \Aimeos\M_Shop::create($context, 'order/product');
        $search = $manager->filter();
        $expr = [$search->compare('==', 'order.product.id', $prod_ids), $search->compare('==', 'order.product.parentid', $order_ids)];
        $search->set_conditions($search->and($expr));
        $rebate = 0;
        foreach ($manager->search($search) as $order_product_item) {
            $rebate += $order_product_item->get_price()->get_rebate();
        }
        return $rebate;
    }
    /**
     * Adds an attribute with the remaining rebate to the order products
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface[] $orderProducts Order product items
     * @param float $remaining Remaining rebate
     * @return \Aimeos\MShop\Order\Item\Product\Iface[] Modified order product items
     */
    protected function set_order_attribute_rebate(array $order_products, float $remaining): array
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'order/product/attribute');
        $order_attr_item = $manager->create();
        $order_attr_item->set_value(number_format($remaining, 2, '.', ''));
        $order_attr_item->set_code('coupon-remain');
        $order_attr_item->set_type('coupon');
        foreach ($order_products as $order_product) {
            $order_product->set_attribute_item(clone $order_attr_item);
        }
        return $order_products;
    }
}