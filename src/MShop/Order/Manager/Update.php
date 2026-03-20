<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Manager;

/**
 * Update trait for order managers
 *
 * @package MShop
 * @subpackage Order
 */
trait Update
{
    /**
     * Returns the context item object.
     *
     * @return \Aimeos\MShop\ContextIface Context item object
     */
    abstract protected function context(): \Aimeos\M_Shop\Context_Iface;
    /**
     * Blocks the resources listed in the order.
     *
     * Every order contains resources like products or redeemed coupon codes
     * that must be blocked so they can't be used by another customer in a
     * later order. This method reduces the the stock level of products, the
     * counts of coupon codes and others.
     *
     * It's save to call this method multiple times for one order. In this case,
     * the actions will be executed only once. All subsequent calls will do
     * nothing as long as the resources haven't been unblocked in the meantime.
     *
     * You can also block and unblock resources several times. Please keep in
     * mind that unblocked resources may be reused by other orders in the
     * meantime. This can lead to an oversell of products!
     *
     * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
     * @return \Aimeos\MShop\Order\Item\Iface Order item object
     */
    public function block(\Aimeos\M_Shop\Order\Item\Iface $order_item): \Aimeos\M_Shop\Order\Item\Iface
    {
        $this->update_status($order_item, \Aimeos\M_Shop\Order\Item\Status\Base::STOCK_UPDATE, 1, -1);
        $this->update_status($order_item, \Aimeos\M_Shop\Order\Item\Status\Base::COUPON_UPDATE, 1, -1);
        return $order_item;
    }
    /**
     * Frees the resources listed in the order.
     *
     * If customers created orders but didn't pay for them, the blocked resources
     * like products and redeemed coupon codes must be unblocked so they can be
     * ordered again or used by other customers. This method increased the stock
     * level of products, the counts of coupon codes and others.
     *
     * It's save to call this method multiple times for one order. In this case,
     * the actions will be executed only once. All subsequent calls will do
     * nothing as long as the resources haven't been blocked in the meantime.
     *
     * You can also unblock and block resources several times. Please keep in
     * mind that unblocked resources may be reused by other orders in the
     * meantime. This can lead to an oversell of products!
     *
     * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
     * @return \Aimeos\MShop\Order\Item\Iface Order item object
     */
    public function unblock(\Aimeos\M_Shop\Order\Item\Iface $order_item): \Aimeos\M_Shop\Order\Item\Iface
    {
        $this->update_status($order_item, \Aimeos\M_Shop\Order\Item\Status\Base::STOCK_UPDATE, 0, +1);
        $this->update_status($order_item, \Aimeos\M_Shop\Order\Item\Status\Base::COUPON_UPDATE, 0, +1);
        return $order_item;
    }
    /**
     * Blocks or frees the resources listed in the order if necessary.
     *
     * After payment status updates, the resources like products or coupon
     * codes listed in the order must be blocked or unblocked. This method
     * cares about executing the appropriate action depending on the payment
     * status.
     *
     * It's save to call this method multiple times for one order. In this case,
     * the actions will be executed only once. All subsequent calls will do
     * nothing as long as the payment status hasn't changed in the meantime.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
     * @return \Aimeos\MShop\Order\Item\Iface Order item object
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order_item): \Aimeos\M_Shop\Order\Item\Iface
    {
        match ($order_item->get_status_payment()) {
            \Aimeos\M_Shop\Order\Item\Base::PAY_DELETED, \Aimeos\M_Shop\Order\Item\Base::PAY_CANCELED, \Aimeos\M_Shop\Order\Item\Base::PAY_REFUSED, \Aimeos\M_Shop\Order\Item\Base::PAY_REFUND => $this->unblock($order_item),
            \Aimeos\M_Shop\Order\Item\Base::PAY_PENDING, \Aimeos\M_Shop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\M_Shop\Order\Item\Base::PAY_RECEIVED => $this->block($order_item),
            default => $order_item,
        };
        return $order_item;
    }
    /**
     * Adds a new status record to the order with the type and value.
     *
     * @param string $parentid Order ID
     * @param string $type Status type
     * @param string $value Status value
     * @return \Aimeos\MShop\Common\Manager\Iface Same manager for fluent interface
     */
    protected function add_status_item(string $parentid, string $type, string $value): Iface
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'order/status');
        $item = $manager->create();
        $item->set_parent_id($parentid);
        $item->set_type($type);
        $item->set_value($value);
        $manager->save($item, false);
        return $this;
    }
    /**
     * Returns the product articles and their bundle product codes for the given article ID
     *
     * @param string $prodId Product ID of the article whose stock level changed
     * @return array Associative list of article codes as keys and lists of bundle product codes as values
     */
    protected function get_bundle_map(string $prod_id): array
    {
        $bundle_map = [];
        $product_manager = \Aimeos\M_Shop::create($this->context(), 'product');
        $search = $product_manager->filter();
        $func = $search->make('product:has', ['product', 'default', $prod_id]);
        $expr = [$search->compare('==', 'product.type', ['bundle', 'group']), $search->compare('!=', $func, null)];
        $search->set_conditions($search->and($expr));
        $search->slice(0, 0x7fffffff);
        $bundle_items = $product_manager->search($search, ['product']);
        foreach ($bundle_items as $bundle_item) {
            foreach ($bundle_item->get_ref_items('product', null, 'default') as $item) {
                $bundle_map[$item->get_id()][] = $bundle_item->get_id();
            }
        }
        return $bundle_map;
    }
    /**
     * Returns the last status item for the given order ID.
     *
     * @param string $parentid Order ID
     * @param string $type Status type constant
     * @param string $status New status value stored along with the order item
     * @return \Aimeos\MShop\Order\Item\Status\Iface|null Order status item or NULL if no item is available
     */
    protected function get_last_status_item(string $parentid, string $type, string $status): ?\Aimeos\M_Shop\Order\Item\Status\Iface
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'order/status');
        $search = $manager->filter();
        $expr = [$search->compare('==', 'order.status.parentid', $parentid), $search->compare('==', 'order.status.type', $type), $search->compare('==', 'order.status.value', $status)];
        $search->set_conditions($search->and($expr));
        $search->set_sortations([$search->sort('-', 'order.status.ctime')]);
        $search->slice(0, 1);
        return $manager->search($search)->first();
    }
    /**
     * Returns the stock items for the given product codes
     *
     * @param iterable $prodIds List of product codes
     * @param string $stockType Stock type code the stock items must belong to
     * @return \Aimeos\Map Associative list of \Aimeos\MShop\Stock\Item\Iface and IDs as values
     */
    protected function get_stock_items(iterable $prod_ids, string $stock_type): \Aimeos\Map
    {
        $stock_manager = \Aimeos\M_Shop::create($this->context(), 'stock');
        $search = $stock_manager->filter()->slice(0, 0x7fffffff)->add(['stock.productid' => $prod_ids, 'stock.type' => $stock_type]);
        return $stock_manager->search($search);
    }
    /**
     * Increases or decreses the coupon code counts referenced in the order by the given value.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
     * @param int $how Positive or negative integer number for increasing or decreasing the coupon count
     * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
     */
    protected function update_coupons(\Aimeos\M_Shop\Order\Item\Iface $order_item, int $how = +1)
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'order/coupon');
        $coupon_code_manager = \Aimeos\M_Shop::create($context, 'coupon/code');
        $search = $manager->filter();
        $search->set_conditions($search->compare('==', 'order.coupon.parentid', $order_item->get_id()));
        $start = 0;
        $coupon_code_manager->begin();
        try {
            do {
                $items = $manager->search($search);
                foreach ($items as $item) {
                    $coupon_code_manager->decrease($item->get_code(), $how * -1);
                }
                $count = count($items);
                $start += $count;
                $search->slice($start);
            } while ($count >= $search->get_limit());
            $coupon_code_manager->commit();
        } catch (\Exception $e) {
            $coupon_code_manager->rollback();
            throw $e;
        }
        return $this;
    }
    /**
     * Increases or decreases the stock level or the coupon code count for referenced items of the given order.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
     * @param string $type Constant from \Aimeos\MShop\Order\Item\Status\Base, e.g. STOCK_UPDATE or COUPON_UPDATE
     * @param string $status New status value stored along with the order item
     * @param int $value Number to increse or decrease the stock level or coupon code count
     * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
     */
    protected function update_status(\Aimeos\M_Shop\Order\Item\Iface $order_item, string $type, string $status, int $value)
    {
        $status_item = $this->get_last_status_item($order_item->get_id(), $type, $status);
        if ($status_item && $status_item->get_value() == $status) {
            return;
        }
        if ($type == \Aimeos\M_Shop\Order\Item\Status\Base::STOCK_UPDATE) {
            $this->update_stock($order_item, $value);
        } elseif ($type == \Aimeos\M_Shop\Order\Item\Status\Base::COUPON_UPDATE) {
            $this->update_coupons($order_item, $value);
        }
        return $this->add_status_item($order_item->get_id(), $type, $status);
    }
    /**
     * Increases or decreases the stock levels of the products referenced in the order by the given value.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
     * @param int $how Positive or negative integer number for increasing or decreasing the stock levels
     * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
     */
    protected function update_stock(\Aimeos\M_Shop\Order\Item\Iface $order_item, int $how = +1)
    {
        $context = $this->context();
        $stock_manager = \Aimeos\M_Shop::create($context, 'stock');
        $manager = \Aimeos\M_Shop::create($context, 'order/product');
        $search = $manager->filter();
        $search->set_conditions($search->compare('==', 'order.product.parentid', $order_item->get_id()));
        $start = 0;
        $stock_manager->begin();
        try {
            do {
                $items = $manager->search($search);
                foreach ($items as $item) {
                    $stock_manager->decrease([$item->get_product_id() => -1 * $how * $item->get_quantity()], $item->get_stock_type());
                    switch ($item->get_type()) {
                        case 'default':
                            $this->update_stock_bundle($item->get_parent_product_id(), $item->get_stock_type());
                            break;
                        case 'select':
                            $this->update_stock_selection($item->get_parent_product_id(), $item->get_stock_type());
                            break;
                    }
                }
                $count = count($items);
                $start += $count;
                $search->slice($start);
            } while ($count >= $search->get_limit());
            $stock_manager->commit();
        } catch (\Exception $e) {
            $stock_manager->rollback();
            throw $e;
        }
        return $this;
    }
    /**
     * Updates the stock levels of bundles for a specific type
     *
     * @param string $prodId Unique product ID
     * @param string $stockType Unique stock type
     * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
     */
    protected function update_stock_bundle(string $prod_id, string $stock_type)
    {
        if (($bundle_map = $this->get_bundle_map($prod_id)) === []) {
            return;
        }
        $bundle_ids = $stock = [];
        foreach ($this->get_stock_items(array_keys($bundle_map), $stock_type) as $stock_item) {
            if (isset($bundle_map[$stock_item->get_product_id()]) && $stock_item->get_stock_level() !== null) {
                foreach ($bundle_map[$stock_item->get_product_id()] as $bundle_id) {
                    if (isset($stock[$bundle_id])) {
                        $stock[$bundle_id] = min($stock[$bundle_id], $stock_item->get_stock_level());
                    } else {
                        $stock[$bundle_id] = $stock_item->get_stock_level();
                    }
                    $bundle_ids[$bundle_id] = null;
                }
            }
        }
        if (empty($stock)) {
            return;
        }
        $stock_manager = \Aimeos\M_Shop::create($this->context(), 'stock');
        foreach ($this->get_stock_items(array_keys($bundle_ids), $stock_type) as $item) {
            if (isset($stock[$item->get_product_id()])) {
                $item->set_stock_level($stock[$item->get_product_id()]);
                $stock_manager->save($item);
            }
        }
        return $this;
    }
    /**
     * Updates the stock levels of selection products for a specific type
     *
     * @param string $prodId Unique product ID
     * @param string $stocktype Unique stock type
     * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
     */
    protected function update_stock_selection(string $prod_id, string $stocktype)
    {
        $stock_manager = \Aimeos\M_Shop::create($this->context(), 'stock');
        $product_manager = \Aimeos\M_Shop::create($this->context(), 'product');
        $product_item = $product_manager->get($prod_id, ['product']);
        $prod_ids = $product_item->get_ref_items('product', 'default', 'default')->get_id()->push($product_item->get_id());
        $stock_items = $this->get_stock_items($prod_ids, $stocktype);
        $sel_stock_item = $stock_items->col(null, 'stock.productid')->pull($prod_id) ?: $stock_manager->create();
        $sum = $stock_items->get_stock_level()->reduce(fn($result, $value) => $result !== null && $value !== null ? $result + $value : null, 0);
        $sel_stock_item->set_product_id($product_item->get_id())->set_type($stocktype)->set_stock_level($sum);
        $stock_manager->save($sel_stock_item, false);
        return $this;
    }
}