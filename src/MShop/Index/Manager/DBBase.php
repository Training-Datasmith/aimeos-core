<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Index
 */
namespace Aimeos\M_Shop\Index\Manager;

/**
 * Base class for all database based index managers
 *
 * @package MShop
 * @subpackage Index
 */
abstract class Db_Base extends \Aimeos\M_Shop\Common\Manager\Base implements \Aimeos\M_Shop\Product\Manager\Iface
{
    private \Aimeos\M_Shop\Common\Manager\Iface $manager;
    /**
     * Initializes the manager object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context)
    {
        parent::__construct($context);
        /** mshop/index/manager/resource
         * Name of the database connection resource to use
         *
         * You can configure a different database connection for each data domain
         * and if no such connection name exists, the "db" connection will be used.
         * It's also possible to use the same database connection for different
         * data domains by configuring the same connection name using this setting.
         *
         * @param string Database connection name
         * @since 2023.04
         */
        $this->set_resource_name($context->config()->get('mshop/index/manager/resource', 'db-product'));
        $this->manager = \Aimeos\M_Shop::create($this->context(), 'product');
    }
    /**
     * Removes old entries from the storage.
     *
     * @param iterable $siteids List of IDs for sites whose entries should be deleted
     * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
     */
    public function clear(iterable $siteids): \Aimeos\M_Shop\Common\Manager\Iface
    {
        foreach ($this->get_sub_managers() as $submanager) {
            $submanager->clear($siteids);
        }
        return $this;
    }
    /**
     * Creates a new empty item instance
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Product\Item\Iface New product item object
     */
    public function create(array $values = []): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->manager->create($values);
    }
    /**
     * Creates a new lists item object
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Common\Item\Lists\Iface New list items object
     */
    public function create_list_item(array $values = []): \Aimeos\M_Shop\Common\Item\Lists\Iface
    {
        return $this->manager->create_list_item($values);
    }
    /**
     * Creates a new property item object
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Common\Item\Property\Iface New property items object
     */
    public function create_property_item(array $values = []): \Aimeos\M_Shop\Common\Item\Property\Iface
    {
        return $this->manager->create_property_item($values);
    }
    /**
     * Removes multiple items.
     *
     * @param \Aimeos\MShop\Common\Item\Iface|\Aimeos\Map|array|string $items Item object, ID or a list of them
     * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
     */
    public function delete($item_ids): \Aimeos\M_Shop\Common\Manager\Iface
    {
        foreach ($this->get_sub_managers() as $submanager) {
            $submanager->delete($item_ids);
        }
        return $this;
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
        return $this->manager->filter($default);
    }
    /**
     * Returns the item specified by its code and domain/type if necessary
     *
     * @param string $code Code of the item
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param string|null $domain Domain of the item if necessary to identify the item uniquely
     * @param string|null $type Type code of the item if necessary to identify the item uniquely
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Common\Item\Iface Item object
     */
    public function find(string $code, array $ref = [], ?string $domain = null, ?string $type = null, ?bool $default = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->manager->find($code, $ref, $domain, $type, $default);
    }
    /**
     * Returns the product item for the given ID
     *
     * @param string $id Id of item
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Product\Item\Iface Product item object
     */
    public function get(string $id, array $ref = [], ?bool $default = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->manager->get($id, $ref, $default);
    }
    /**
     * Returns a list of attribute objects describing the available criteria for searching
     *
     * @param bool $withsub True to return attributes of sub-managers too
     * @return array List of items implementing \Aimeos\Base\Criteria\Attribute\Iface
     */
    public function get_search_attributes(bool $withsub = true): array
    {
        return $this->manager->get_search_attributes($withsub);
    }
    /**
     * Iterates over all matched items and returns the found ones
     *
     * @param \Aimeos\MShop\Common\Cursor\Iface $cursor Cursor object with filter, domains and cursor
     * @param string[] $ref List of domains whose items should be fetched too
     * @return \Aimeos\Map|null List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
     */
    public function iterate(\Aimeos\M_Shop\Common\Cursor\Iface $cursor, array $ref = []): ?\Aimeos\Map
    {
        if ($cursor->value() === '') {
            return null;
        }
        $filter = $cursor->filter()->add('product.id', '>', (int) $cursor->value())->order('product.id');
        $items = $this->search($filter, $ref);
        $cursor->set_value($items->last_key() ?: '');
        return !$items->is_empty() ? $items : null;
    }
    /**
     * Updates the rating of the item
     *
     * @param string $id ID of the item
     * @param string $rating Decimal value of the rating
     * @param int $ratings Total number of ratings for the item
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
     */
    public function rate(string $id, string $rating, int $ratings): \Aimeos\M_Shop\Common\Manager\Iface
    {
        $this->manager->rate($id, $rating, $ratings);
        return $this;
    }
    /**
     * Rebuilds the customer index
     *
     * @param \Aimeos\MShop\Product\Item\Iface[] $items Associative list of product IDs and items values
     * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
     */
    public function rebuild(iterable $items = []): \Aimeos\M_Shop\Index\Manager\Iface
    {
        foreach ($this->get_sub_managers() as $submanager) {
            $submanager->rebuild($items);
        }
        return $this;
    }
    /**
     * Removes the products from the product index.
     *
     * @param iterable|string $ids Product ID or list of IDs
     * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
     */
    public function remove($ids): \Aimeos\M_Shop\Index\Manager\Iface
    {
        foreach ($this->get_sub_managers() as $submanager) {
            $submanager->remove($ids);
        }
        return $this;
    }
    /**
     * Adds or updates an item object or a list of them.
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Saved item or items
     */
    public function save($items, bool $fetch = true)
    {
        $this->rebuild(map($this->manager->save($items, true)));
        return $items;
    }
    /**
     * Updates if the product is in stock
     *
     * @param string $id ID of the procuct item
     * @param int $value "0" or "1" if product is in stock or not
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
     */
    public function stock(string $id, int $value): \Aimeos\M_Shop\Common\Manager\Iface
    {
        $this->manager->stock($id, $value);
        return $this;
    }
    /**
     * Removes all entries not touched after the given timestamp
     *
     * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
     * @param string $path Configuration path to the SQL statement to execute
     * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
     */
    protected function cleanup_base(string $timestamp, string $path): \Aimeos\M_Shop\Index\Manager\Iface
    {
        $context = $this->context();
        $siteid = $context->locale()->get_site_id();
        $this->begin();
        $conn = $context->db($this->get_resource_name());
        try {
            $stmt = $this->get_cached_statement($conn, $path);
            $stmt->bind(1, $timestamp);
            // ctime
            $stmt->bind(2, $siteid);
            $stmt->execute()->finish();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
        $this->commit();
        foreach ($this->get_sub_managers() as $submanager) {
            $submanager->cleanup($timestamp);
        }
        return $this;
    }
    /**
     * Removes several items from the index
     *
     * @param string[] $ids List of product IDs
     * @param string $path Configuration path to the SQL statement to execute
     * @param bool $siteidcheck If siteid should be used in the statement
     * @param string $name Name of the ID column
     * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
     */
    protected function delete_items_base($ids, string $path, bool $siteidcheck = true, string $name = 'prodid'): \Aimeos\M_Shop\Common\Manager\Iface
    {
        foreach ($this->get_sub_managers() as $submanager) {
            $submanager->delete($ids);
        }
        return parent::delete_items_base($ids, $path, $siteidcheck, $name);
    }
    /**
     * Returns the product manager instance
     *
     * @return \Aimeos\MShop\Product\Manager\Iface Product manager object
     */
    protected function get_manager(): \Aimeos\M_Shop\Common\Manager\Iface
    {
        return $this->manager;
    }
    /**
     * Returns the string replacements for the SQL statements
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search critera object
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attronly Associative list of search keys and criteria attribute items as values for the base table
     * @param \Aimeos\Base\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
     * @param string[] $joins Associative list of SQL joins
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $columns Additional columns to retrieve values from
     * @return array Array of keys, find and replace arrays
     */
    protected function get_sql_replacements(\Aimeos\Base\Criteria\Iface $search, array $attributes, array $attronly, array $plugins, array $joins): array
    {
        $types = $this->get_search_types($attributes);
        $funcs = $this->get_search_functions($attributes);
        $translations = $this->get_search_translations($attributes);
        $translations = $this->alias_translations($translations);
        if (!empty($sorts = $search->get_sortations())) {
            $names = $search->translate($sorts, [], $funcs);
            $cols = $search->translate($sorts, $translations, $funcs);
            $ops = map($sorts)->get_operator();
            $list = $translations = [];
            foreach ($cols as $idx => $col) {
                if (!str_contains($col, '"')) {
                    $col = $this->alias($names[$idx]) . '."' . $col . '"';
                }
                $list[] = ($ops[$idx] === '-' ? 'MAX' : 'MIN') . '(' . $col . ') AS "s' . $idx . '"';
                $translations[$names[$idx]] = '"s' . $idx . '"';
            }
        }
        $map = parent::get_sql_replacements($search, $attributes, $attronly, $plugins, $joins);
        $map[':mincols'] = !empty($list) ? ', ' . implode(', ', $list) : '';
        $map[':order'] = $search->get_sortation_source($types, $translations, $funcs);
        return $map;
    }
    /**
     * Optimizes the catalog customer index if necessary
     *
     * @param string $path Configuration path to the SQL statements to execute
     * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
     */
    protected function optimize_base(string $path): \Aimeos\M_Shop\Index\Manager\Iface
    {
        $context = $this->context();
        $conn = $context->db($this->get_resource_name());
        foreach ((array) $this->get_sql_config($path) as $sql) {
            $conn->create($sql)->execute()->finish();
        }
        foreach ($this->get_sub_managers() as $submanager) {
            $submanager->optimize();
        }
        return $this;
    }
    /**
     * Searches for items matching the given criteria.
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search criteria
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param int &$total Total number of items matched by the given criteria
     * @param string $cfgPathSearch Configuration path to the search SQL statement
     * @param string $cfgPathCount Configuration path to the count SQL statement
     * @return \Aimeos\MShop\Product\Item\Iface[] List of product items
     */
    protected function search_items_index_base(\Aimeos\Base\Criteria\Iface $search, array $ref, ?int &$total, string $cfg_path_search, string $cfg_path_count): \Aimeos\Map
    {
        $list = $ids = [];
        $context = $this->context();
        $conn = $context->db($this->get_resource_name());
        $required = ['product'];
        /** mshop/index/manager/sitemode
         * Mode how items from levels below or above in the site tree are handled
         *
         * By default, only items from the current site are fetched from the
         * storage. If the ai-sites extension is installed, you can create a
         * tree of sites. Then, this setting allows you to define for the
         * whole index domain if items from parent sites are inherited,
         * sites from child sites are aggregated or both.
         *
         * Available constants for the site mode are:
         * * 0 = only items from the current site
         * * 1 = inherit items from parent sites
         * * 2 = aggregate items from child sites
         * * 3 = inherit and aggregate items at the same time
         *
         * You also need to set the mode in the locale manager
         * (mshop/locale/manager/sitelevel) to one of the constants.
         * If you set it to the same value, it will work as described but you
         * can also use different modes. For example, if inheritance and
         * aggregation is configured the locale manager but only inheritance
         * in the domain manager because aggregating items makes no sense in
         * this domain, then items wil be only inherited. Thus, you have full
         * control over inheritance and aggregation in each domain.
         *
         * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
         * @since 2018.01
         * @see mshop/locale/manager/sitelevel
         */
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL;
        $level = $context->config()->get('mshop/index/manager/sitemode', $level);
        $results = $this->search_items_base($conn, $search, $cfg_path_search, $cfg_path_count, $required, $total, $level);
        while ($row = $results->fetch()) {
            $ids[] = $row['id'];
        }
        $manager = \Aimeos\M_Shop::create($context, 'product');
        $prod_search = $manager->filter();
        $prod_search->set_conditions($prod_search->compare('==', 'product.id', $ids));
        $prod_search->slice(0, $search->get_limit());
        $items = $manager->search($prod_search, $ref);
        foreach ($ids as $id) {
            if (isset($items[$id])) {
                $list[$id] = $items[$id];
            }
        }
        return map($list);
    }
}