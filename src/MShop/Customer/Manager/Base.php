<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Customer
 */
namespace Aimeos\M_Shop\Customer\Manager;

/**
 * Base class with common methods for all customer implementations.
 *
 * @package MShop
 * @subpackage Customer
 */
abstract class Base extends \Aimeos\M_Shop\Common\Manager\Base
{
    /**
     * Counts the number items that are available for the values of the given key.
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search criteria
     * @param array|string $key Search key or list of key to aggregate items for
     * @param string|null $value Search key for aggregating the value column
     * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
     * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
     */
    public function aggregate(\Aimeos\Base\Criteria\Iface $search, $key, ?string $value = null, ?string $type = null): \Aimeos\Map
    {
        /** mshop/customer/manager/aggregate/mysql
         * Counts the number of records grouped by the values in the key column and matched by the given criteria
         *
         * @see mshop/customer/manager/aggregate/ansi
         */
        /** mshop/customer/manager/aggregate/ansi
         * Counts the number of records grouped by the values in the key column and matched by the given criteria
         *
         * Groups all records by the values in the key column and counts their
         * occurence. The matched records can be limited by the given criteria
         * from the customer database. The records must be from one of the sites
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
         * @param string SQL statement for aggregating customer items
         * @since 2021.04
         * @see mshop/customer/manager/insert/ansi
         * @see mshop/customer/manager/update/ansi
         * @see mshop/customer/manager/newid/ansi
         * @see mshop/customer/manager/delete/ansi
         * @see mshop/customer/manager/search/ansi
         * @see mshop/customer/manager/count/ansi
         */
        $cfgkey = 'mshop/customer/manager/aggregate';
        return $this->aggregate_base($search, $key, $cfgkey, ['customer'], $value, $type);
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
        return $this->filter_base('customer', $default);
    }
    /**
     * Returns the item specified by its code and domain/type if necessary
     *
     * @param string $code Code of the item
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param string|null $domain Domain of the item if necessary to identify the item uniquely
     * @param string|null $type Type code of the item if necessary to identify the item uniquely
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Customer\Item\Iface Item object
     */
    public function find(string $code, array $ref = [], ?string $domain = null, ?string $type = null, ?bool $default = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->find_base(['customer.code' => $code], $ref, $default);
    }
    /**
     * Returns the customer item object specificed by its ID.
     *
     * @param string $id Unique customer ID referencing an existing customer
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Customer\Item\Iface Returns the customer item of the given id
     * @throws \Aimeos\MShop\Exception If item couldn't be found
     */
    public function get(string $id, array $ref = [], ?bool $default = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->get_item_base('customer.id', $id, $ref, $default);
    }
    /**
     * Adds the customer to the groups listed in the customer item
     *
     * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item
     * @return \Aimeos\MShop\Customer\Item\Iface $item Modified customer item
     */
    protected function add_groups(\Aimeos\M_Shop\Customer\Item\Iface $item): \Aimeos\M_Shop\Customer\Item\Iface
    {
        $pos = 0;
        $manager = $this->object()->get_sub_manager('lists');
        $list_items = $item->get_list_items('group', 'default', null, false);
        foreach ($item->get_groups() as $ref_id) {
            if (($litem = $item->get_list_item('group', 'default', $ref_id, false)) !== null) {
                unset($list_items[$litem->get_id()], $list_items['__group_default_' . $ref_id]);
            } else {
                $litem = $manager->create()->set_type('default');
            }
            $item->add_list_item('group', $litem->set_ref_id($ref_id)->set_position($pos++));
        }
        return $item->delete_list_items($list_items);
    }
    /**
     * Creates a new customer item.
     *
     * @param array $values List of attributes for customer item
     * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
     * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
     * @param \Aimeos\MShop\Common\Item\Address\Iface[] $addrItems List of address items
     * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
     * @return \Aimeos\MShop\Customer\Item\Iface New customer item
     */
    protected function create_item_base(array $values = [], array $list_items = [], array $ref_items = [], array $addr_items = [], array $prop_items = []): \Aimeos\M_Shop\Common\Item\Iface
    {
        $values['.listitems'] = $list_items;
        $values['.propitems'] = $prop_items;
        $values['.addritems'] = $addr_items;
        return $this->create($values);
    }
    /**
     * Deletes items.
     *
     * @param \Aimeos\MShop\Common\Item\Iface|\Aimeos\Map|array|string $items List of item objects or IDs of the items
     * @param string $cfgpath Configuration path to the SQL statement
     * @param bool $siteid If siteid should be used in the statement
     * @param string $name Name of the ID column
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
     */
    protected function delete_items_base($items, string $cfgpath, bool $siteid = true, string $name = 'id'): \Aimeos\M_Shop\Common\Manager\Iface
    {
        if (map($items)->is_empty()) {
            return $this;
        }
        $search = $this->object()->filter();
        $search->set_conditions($search->compare('==', $name, $items));
        $types = [$name => \Aimeos\Base\DB\Statement\Base::PARAM_STR];
        $translations = [$name => '"' . $name . '"'];
        $cond = $search->get_condition_source($types, $translations);
        $sql = str_replace(':cond', $cond, $this->get_sql_config($cfgpath));
        $context = $this->context();
        $conn = $context->db($this->get_resource_name());
        $stmt = $conn->create($sql);
        if ($siteid) {
            $stmt->bind(1, $context->locale()->get_site_id() . '%');
            $stmt->bind(2, $context->user()?->get_site_id());
        }
        $stmt->execute()->finish();
        return $this;
    }
}