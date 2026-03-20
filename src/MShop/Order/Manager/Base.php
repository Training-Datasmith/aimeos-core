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
 * Basic methods and constants for order items (shopping basket).
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\M_Shop\Common\Manager\Base
{
    /**
     * Returns the address item map for the given order IDs
     *
     * @param string[] $ids List of order IDs
     * @param array $ref List of referenced domains that should be fetched too
     * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order address ID/item pairs as values
     */
    protected function get_addresses(array $ids, array $ref): \Aimeos\Map
    {
        $manager = $this->object()->get_sub_manager('address');
        $filter = $manager->filter()->add('order.address.parentid', '==', $ids)->order(['order.address.type', 'order.address.position', 'order.address.id'])->slice(0, 0x7fffffff);
        return $manager->search($filter, $ref)->group_by('order.address.parentid');
    }
    /**
     * Returns the coupon map for the given order IDs
     *
     * @param string[] $ids List of order IDs
     * @param array $ref List of referenced domains that should be fetched too
     * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order coupon ID/item pairs as values
     */
    protected function get_coupons(array $ids, array $ref): \Aimeos\Map
    {
        $manager = $this->object()->get_sub_manager('coupon');
        $filter = $manager->filter()->add('order.coupon.parentid', '==', $ids)->order('order.coupon.code')->slice(0, 0x7fffffff);
        return $manager->search($filter, $ref)->group_by('order.coupon.parentid');
    }
    /**
     * Retrieves the ordered products from the storage.
     *
     * @param string[] $ids List of order IDs
     * @param array $ref List of referenced domains that should be fetched too
     * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order product ID/item pairs as values
     */
    protected function get_products(array $ids, array $ref): \Aimeos\Map
    {
        $manager = $this->object()->get_sub_manager('product');
        $filter = $manager->filter()->add('order.product.parentid', '==', $ids)->order('order.product.position')->slice(0, 0x7fffffff);
        $items = $manager->search($filter, $ref);
        $map = $items->group_by('order.product.orderproductid');
        foreach ($map as $id => $list) {
            $items[$id]?->set_products($list);
        }
        return map($map->get(''))->group_by('order.product.parentid');
    }
    /**
     * Retrieves the order services from the storage.
     *
     * @param string[] $ids List of order IDs
     * @param array $ref List of referenced domains that should be fetched too
     * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and service ID/item pairs as values
     */
    protected function get_services(array $ids, array $ref): \Aimeos\Map
    {
        $manager = $this->object()->get_sub_manager('service');
        $filter = $manager->filter()->add('order.service.parentid', '==', $ids)->order(['order.service.type', 'order.service.position', 'order.service.id'])->slice(0, 0x7fffffff);
        return $manager->search($filter, $ref)->group_by('order.service.parentid');
    }
    /**
     * Retrieves the order statuses from the storage.
     *
     * @param string[] $ids List of order IDs
     * @param array $ref List of referenced domains that should be fetched too
     * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order status ID/item pairs as values
     */
    protected function get_statuses(array $ids, array $ref): \Aimeos\Map
    {
        $manager = $this->object()->get_sub_manager('status');
        $filter = $manager->filter()->add('order.status.parentid', '==', $ids)->slice(0, 0x7fffffff);
        return $manager->search($filter, $ref)->group_by('order.status.parentid');
    }
    /**
     * Saves the addresses of the order to the storage.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $item Order containing address items
     * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
     */
    protected function save_addresses(\Aimeos\M_Shop\Order\Item\Iface $item): \Aimeos\M_Shop\Order\Manager\Iface
    {
        $addresses = $item->get_addresses();
        foreach ($addresses as $list) {
            $pos = 0;
            foreach ($list as $address) {
                if ($address->get_parent_id() != $item->get_id()) {
                    $address->set_id(null);
                    // create new item if copied
                }
                $address->set_parent_id($item->get_id())->set_position(++$pos);
            }
        }
        $this->object()->get_sub_manager('address')->save($addresses->flat(1));
        return $this;
    }
    /**
     * Saves the coupons of the order to the storage.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $item Order containing coupon items
     * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
     */
    protected function save_coupons(\Aimeos\M_Shop\Order\Item\Iface $item): \Aimeos\M_Shop\Order\Manager\Iface
    {
        $list = [];
        $manager = $this->object()->get_sub_manager('coupon');
        $filter = $manager->filter()->add('order.coupon.parentid', '==', $item->get_id())->slice(0, 0x7fffffff);
        $items = $manager->search($filter)->group_by('order.coupon.code');
        foreach ($item->get_coupons() as $code => $products) {
            if (empty($products)) {
                $list[] = current($items[$code]) ?: $manager->create()->set_parent_id($item->get_id())->set_code($code);
                continue;
            }
            foreach ($products as $product) {
                foreach ($items[$code] ?? [] as $prod_item) {
                    if ($product->get_id() === $prod_item->get_id()) {
                        continue 2;
                    }
                }
                $list[] = $manager->create()->set_parent_id($item->get_id())->set_code($code)->set_product_id($product->get_id());
            }
        }
        $manager->save($list);
        return $this;
    }
    /**
     * Saves the ordered products to the storage.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $item Order containing ordered products or bundles
     * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
     */
    protected function save_products(\Aimeos\M_Shop\Order\Item\Iface $item): \Aimeos\M_Shop\Order\Manager\Iface
    {
        $products = $item->get_products();
        $pos = (int) $products->merge($products->get_products()->flat(1))->max('order.product.position');
        foreach ($products as $product) {
            if ($product->get_parent_id() != $item->get_id()) {
                $product->set_id(null);
                // create new item if copied
            }
            if (!$product->get_position()) {
                $product->set_position(++$pos);
            }
            $product->set_parent_id($item->get_id());
            foreach ($product->get_products() as $sub_product) {
                if ($sub_product->get_parent_id() != $item->get_id()) {
                    $sub_product->set_id(null);
                    // create new item if copied
                }
                if (!$sub_product->get_position()) {
                    $sub_product->set_position(++$pos);
                }
                $sub_product->set_parent_id($item->get_id());
            }
        }
        $this->object()->get_sub_manager('product')->save($products);
        return $this;
    }
    /**
     * Saves the services of the order to the storage.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $item Order containing service items
     * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
     */
    protected function save_services(\Aimeos\M_Shop\Order\Item\Iface $item): \Aimeos\M_Shop\Order\Manager\Iface
    {
        $services = $item->get_services();
        foreach ($services as $list) {
            $pos = 0;
            foreach ($list as $service) {
                if ($service->get_parent_id() != $item->get_id()) {
                    $service->set_id(null);
                    // create new item if copied
                }
                $service->set_parent_id($item->get_id())->set_position(++$pos);
            }
        }
        $this->object()->get_sub_manager('service')->save($services->flat(1));
        return $this;
    }
    /**
     * Saves the statuses of the order to the storage.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $item Order containing status items
     * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
     */
    protected function save_statuses(\Aimeos\M_Shop\Order\Item\Iface $item): \Aimeos\M_Shop\Order\Manager\Iface
    {
        $statuses = $item->get_statuses();
        foreach ($statuses as $list) {
            foreach ($list as $status) {
                if ($status->get_parent_id() != $item->get_id()) {
                    $status->set_id(null);
                    // create new item if copied
                }
                $status->set_parent_id($item->get_id());
            }
        }
        $this->object()->get_sub_manager('status')->save($statuses->flat(1));
        return $this;
    }
}