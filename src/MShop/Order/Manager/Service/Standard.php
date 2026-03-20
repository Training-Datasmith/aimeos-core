<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Manager\Service;

/**
 * Default order service manager implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Common\Manager\Base implements \Aimeos\M_Shop\Order\Manager\Service\Iface, \Aimeos\M_Shop\Common\Manager\Factory\Iface
{
    private array $search_config = ['order.service.parentid' => ['label' => 'Order ID', 'internalcode' => 'parentid', 'type' => 'int', 'public' => false], 'order.service.serviceid' => ['label' => 'Service original service ID', 'internalcode' => 'servid', 'public' => false], 'order.service.name' => ['label' => 'Service name', 'internalcode' => 'name'], 'order.service.code' => ['label' => 'Service code', 'internalcode' => 'code'], 'order.service.type' => ['label' => 'Service type', 'internalcode' => 'type'], 'order.service.mediaurl' => ['label' => 'Service media url', 'internalcode' => 'mediaurl', 'public' => false], 'order.service.position' => ['label' => 'Service position', 'internalcode' => 'pos', 'type' => 'int', 'public' => false]];
    /**
     * Counts the number items that are available for the values of the given key.
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search criteria
     * @param array|string $key Search key or list of keys to aggregate items for
     * @param string|null $value Search key for aggregating the value column
     * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
     * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
     */
    public function aggregate(\Aimeos\Base\Criteria\Iface $search, $key, ?string $value = null, ?string $type = null): \Aimeos\Map
    {
        /** mshop/order/manager/service/aggregate/mysql
         * Counts the number of records grouped by the values in the key column and matched by the given criteria
         *
         * @see mshop/order/manager/service/aggregate/ansi
         */
        /** mshop/order/manager/service/aggregate/ansi
         * Counts the number of records grouped by the values in the key column and matched by the given criteria
         *
         * Groups all records by the values in the key column and counts their
         * occurence. The matched records can be limited by the given criteria
         * from the order database. The records must be from one of the sites
         * that are configured via the context item. If the current site is part
         * of a tree of sites, the statement can count all records from the
         * current site and the complete sub-tree of sites.
         *
         * As the records can normally be limited by criteria from sub-managers,
         * their tables must be joined in the SQL context. This is done by
         * using the "internaldeps" property from the definition of the ID
         * column of the sub-managers. These internal dependencies specify
         * the JOIN between the tables and the used columns for joining. The
         * ":joins" placeholder is then replaced by the JOIN strings from
         * the sub-managers.
         *
         * To limit the records matched, conditions can be added to the given
         * criteria object. It can contain comparisons like column names that
         * must match specific values which can be combined by AND, OR or NOT
         * operators. The resulting string of SQL conditions replaces the
         * ":cond" placeholder before the statement is sent to the database
         * server.
         *
         * This statement doesn't return any records. Instead, it returns pairs
         * of the different values found in the key column together with the
         * number of records that have been found for that key values.
         *
         * The SQL statement should conform to the ANSI standard to be
         * compatible with most relational database systems. This also
         * includes using double quotes for table and column names.
         *
         * @param string SQL statement for aggregating order items
         * @since 2014.09
         * @see mshop/order/manager/service/insert/ansi
         * @see mshop/order/manager/service/update/ansi
         * @see mshop/order/manager/service/newid/ansi
         * @see mshop/order/manager/service/delete/ansi
         * @see mshop/order/manager/service/search/ansi
         * @see mshop/order/manager/service/count/ansi
         */
        /** mshop/order/manager/service/aggregateavg/mysql
         * Computes the average of all values grouped by the key column and matched by the given criteria
         *
         * @param string SQL statement for aggregating the order service items and computing the average value
         * @since 2017.10
         * @see mshop/order/manager/service/aggregateavg/ansi
         * @see mshop/order/manager/service/aggregate/mysql
         */
        /** mshop/order/manager/service/aggregateavg/ansi
         * Computes the average of all values grouped by the key column and matched by the given criteria
         *
         * @param string SQL statement for aggregating the order service items and computing the average value
         * @since 2017.10
         * @see mshop/order/manager/service/aggregate/ansi
         */
        /** mshop/order/manager/service/aggregatesum/mysql
         * Computes the sum of all values grouped by the key column and matched by the given criteria
         *
         * @param string SQL statement for aggregating the order service items and computing the sum
         * @since 2017.10
         * @see mshop/order/manager/service/aggregatesum/ansi
         * @see mshop/order/manager/service/aggregate/mysql
         */
        /** mshop/order/manager/service/aggregatesum/ansi
         * Computes the sum of all values grouped by the key column and matched by the given criteria
         *
         * @param string SQL statement for aggregating the order service items and computing the sum
         * @since 2017.10
         * @see mshop/order/manager/service/aggregate/ansi
         */
        $cfgkey = 'mshop/order/manager/service/aggregate';
        return $this->aggregate_base($search, $key, $cfgkey, ['order.service'], $value, $type);
    }
    /**
     * Creates a new empty item instance
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Order\Item\Service\Iface New order service item object
     */
    public function create(array $values = []): \Aimeos\M_Shop\Common\Item\Iface
    {
        $context = $this->context();
        $values['.price'] ??= \Aimeos\M_Shop::create($context, 'price')->create();
        $values['order.service.siteid'] ??= $context->locale()->get_site_id();
        return new \Aimeos\M_Shop\Order\Item\Service\Standard('order.service.', $values);
    }
    /**
     * Creates a new order service attribute item instance
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface New order service attribute item object
     */
    public function create_attribute_item(array $values = []): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->object()->get_sub_manager('attribute')->create($values);
    }
    /**
     * Creates a new order service transaction item instance
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface New order service transaction item object
     */
    public function create_transaction(array $values = []): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->object()->get_sub_manager('transaction')->create($values);
    }
    /**
     * Creates a filter object.
     *
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @param bool $site TRUE for adding site criteria to limit items by the site of related items
     * @return \Aimeos\Base\Criteria\Iface Returns the filter object
     */
    public function filter(?bool $default = false, bool $site = false): \Aimeos\Base\Criteria\Iface
    {
        return parent::filter($default)->order('order.service.id');
    }
    /**
     * Returns the additional column/search definitions
     *
     * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
     */
    public function get_save_attributes(): array
    {
        return $this->create_attributes($this->search_config);
    }
    /**
     * Returns the attributes that can be used for searching.
     *
     * @param bool $withsub Return also attributes of sub-managers if true
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
     */
    public function get_search_attributes(bool $withsub = true): array
    {
        return array_replace(parent::get_search_attributes($withsub), $this->create_attributes(['order.service.id' => ['label' => 'Service ID', 'internaldeps' => ['LEFT JOIN "mshop_order_service" AS mordse ON ( mord."id" = mordse."parentid" )'], 'internalcode' => 'id', 'type' => 'int', 'public' => false], 'order.service.currencyid' => ['label' => 'Service currencyid code', 'internalcode' => 'currencyid'], 'order.service.price' => ['label' => 'Service price', 'internalcode' => 'price', 'type' => 'decimal'], 'order.service.costs' => ['label' => 'Service shipping', 'internalcode' => 'costs', 'type' => 'decimal'], 'order.service.rebate' => ['label' => 'Service rebate', 'internalcode' => 'rebate', 'type' => 'decimal'], 'order.service.taxrates' => ['label' => 'Service taxrates', 'internalcode' => 'taxrate', 'type' => 'json'], 'order.service.taxvalue' => ['label' => 'Service tax value', 'internalcode' => 'tax', 'type' => 'decimal'], 'order.service.taxflag' => ['label' => 'Service tax flag (0=net, 1=gross)', 'internalcode' => 'taxflag', 'type' => 'int']]));
    }
    /**
     * Saves the dependent items of the item
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item object
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Common\Item\Iface Updated item
     */
    public function save_refs(\Aimeos\M_Shop\Common\Item\Iface $item, bool $fetch = true): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->save_attribute_items($item, $fetch);
        $this->save_transactions($item, $fetch);
        return $item;
    }
    /**
     * Merges the data from the given map and the referenced items
     *
     * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
     * @param array $ref List of referenced items to fetch and add to the entries
     * @return array Associative list of ID as key and the updated entries as value
     */
    public function search_refs(array $entries, array $ref): array
    {
        $serv_items = [];
        $parent_ids = array_keys($entries);
        $manager = \Aimeos\M_Shop::create($this->context(), 'price');
        $attributes = $this->get_attribute_items($parent_ids);
        $transactions = $this->get_transactions($parent_ids);
        if ($this->has_ref($ref, 'service')) {
            $serv_items = $this->get_service_items(map($entries)->col('order.service.serviceid')->filter()->all(), $ref);
        }
        foreach ($entries as $id => $row) {
            $entries[$id]['.price'] = $manager->create(['price.currencyid' => $row['order.service.currencyid'], 'price.taxrates' => $row['order.service.taxrates'], 'price.value' => $row['order.service.price'], 'price.costs' => $row['order.service.costs'], 'price.rebate' => $row['order.service.rebate'], 'price.taxflag' => $row['order.service.taxflag'], 'price.taxvalue' => $row['order.service.taxvalue'], 'price.siteid' => $row['order.service.siteid']]);
            $entries[$id]['.service'] = $serv_items[$row['order.service.serviceid']] ?? null;
            $entries[$id]['.transactions'] = $transactions[$id] ?? map();
            $entries[$id]['.attributes'] = $attributes[$id] ?? map();
        }
        return $entries;
    }
    /**
     * Binds additional values to the statement before execution.
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item object
     * @param \Aimeos\Base\DB\Statement\Iface $stmt Database statement object
     * @param int $idx Current bind index
     * @return \Aimeos\Base\DB\Statement\Iface Database statement object with bound values
     */
    protected function bind(\Aimeos\M_Shop\Common\Item\Iface $item, \Aimeos\Base\DB\Statement\Iface $stmt, int &$idx): \Aimeos\Base\DB\Statement\Iface
    {
        $price = $item->get_price();
        $stmt->bind($idx++, $price->get_currency_id());
        $stmt->bind($idx++, $price->get_value());
        $stmt->bind($idx++, $price->get_costs());
        $stmt->bind($idx++, $price->get_rebate());
        $stmt->bind($idx++, $price->get_tax_value());
        $stmt->bind($idx++, json_encode($price->get_tax_rates(), JSON_FORCE_OBJECT));
        $stmt->bind($idx++, $price->get_tax_flag(), \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        return $stmt;
    }
    /**
     * Searches for attribute items connected with order service item.
     *
     * @param string[] $ids List of order service item IDs
     * @return \Aimeos\Map Associative list of order service IDs as keys and order service attribute items
     *  implementing \Aimeos\MShop\Order\Item\Service\Attribute\Iface as values
     */
    protected function get_attribute_items(array $ids): \Aimeos\Map
    {
        $manager = $this->object()->get_sub_manager('attribute');
        $filter = $manager->filter()->add('order.service.attribute.parentid', '==', $ids)->slice(0, 0x7fffffff);
        return $manager->search($filter)->group_by('order.service.attribute.parentid');
    }
    /**
     * Fetches service items connected with order service item.
     *
     * @param string[] $ids List of order service item IDs
     * @return \Aimeos\Map Associative list of order service IDs as keys and order service attribute items
     *  implementing \Aimeos\MShop\Order\Item\Service\Attribute\Iface as values
     */
    protected function get_service_items(array $ids, array $ref): \Aimeos\Map
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'service');
        $search = $manager->filter()->add('service.id', '==', array_unique($ids))->slice(0, 0x7fffffff);
        return $manager->search($search, $ref);
    }
    /**
     * Searches for transaction items connected with order service item.
     *
     * @param string[] $ids List of order service item IDs
     * @return \Aimeos\Map Associative list of order service IDs as keys and order service transaction items
     *  implementing \Aimeos\MShop\Order\Item\Service\Transaction\Iface as values
     */
    protected function get_transactions(array $ids): \Aimeos\Map
    {
        $manager = $this->object()->get_sub_manager('transaction');
        $filter = $manager->filter()->add('order.service.transaction.parentid', '==', $ids)->slice(0, 0x7fffffff);
        return $manager->search($filter)->group_by('order.service.attribute.parentid');
    }
    /**
     * Checks if the item is modified
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item object
     * @return bool True if the item is modified, false if not
     */
    protected function is_modified(\Aimeos\M_Shop\Common\Item\Iface $item): bool
    {
        if ($item->is_modified()) {
            return true;
        }
        return (bool) $item->get_price()->is_modified();
    }
    /**
     * Returns the prefix for the item properties and search keys.
     *
     * @return string Prefix for the item properties and search keys
     */
    protected function prefix(): string
    {
        return 'order.service.';
    }
    /**
     * Saves the attribute items included in the order service item
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface $item Order service item with attribute items
     * @param bool $fetch True if the new ID should be set in the attribute item
     * @return \Aimeos\MShop\Order\Item\Service\Iface Object with saved attribute items and IDs
     */
    protected function save_attribute_items(\Aimeos\M_Shop\Order\Item\Service\Iface $item, bool $fetch): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        $attr_items = $item->get_attribute_items();
        foreach ($attr_items as $key => $attr_item) {
            if ($attr_item->get_type() === 'session') {
                unset($attr_items[$key]);
                continue;
            }
            if ($attr_item->get_parent_id() != $item->get_id()) {
                $attr_item->set_id(null);
                // create new property item if copied
            }
            $attr_item->set_parent_id($item->get_id());
        }
        $manager = $this->object()->get_sub_manager('attribute');
        $manager->delete($item->get_attribute_items_deleted());
        $manager->save($attr_items, $fetch);
        return $item;
    }
    /**
     * Saves the transaction items included in the order service item
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface $item Order service item with transaction items
     * @param bool $fetch True if the new ID should be set in the transaction item
     * @return \Aimeos\MShop\Order\Item\Service\Iface Object with saved transaction items and IDs
     */
    protected function save_transactions(\Aimeos\M_Shop\Order\Item\Service\Iface $item, bool $fetch): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        $list = $item->get_transactions();
        foreach ($list as $tx_item) {
            if ($tx_item->get_parent_id() != $item->get_id()) {
                $tx_item->set_id(null);
                // create new property item if copied
            }
            $tx_item->set_parent_id($item->get_id());
        }
        $this->object()->get_sub_manager('transaction')->save($list, $fetch);
        return $item;
    }
}