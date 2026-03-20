<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Catalog
 */
namespace Aimeos\M_Shop\Catalog\Manager;

/**
 * Catalog manager with methods for managing categories products, text, media.
 *
 * @package MShop
 * @subpackage Catalog
 */
class Standard extends Base implements \Aimeos\M_Shop\Catalog\Manager\Iface, \Aimeos\M_Shop\Common\Manager\Factory\Iface
{
    /** mshop/catalog/manager/name
     * Class name of the used catalog manager implementation
     *
     * Each default manager can be replace by an alternative imlementation.
     * To use this implementation, you have to set the last part of the class
     * name as configuration value so the manager factory knows which class it
     * has to instantiate.
     *
     * For example, if the name of the default class is
     *
     *  \Aimeos\MShop\Catalog\Manager\Standard
     *
     * and you want to replace it with your own version named
     *
     *  \Aimeos\MShop\Catalog\Manager\Mymanager
     *
     * then you have to set the this configuration option:
     *
     *  mshop/catalog/manager/name = Mymanager
     *
     * The value is the last part of your own class name and it's case sensitive,
     * so take care that the configuration value is exactly named like the last
     * part of the class name.
     *
     * The allowed characters of the class name are A-Z, a-z and 0-9. No other
     * characters are possible! You should always start the last part of the class
     * name with an upper case character and continue only with lower case characters
     * or numbers. Avoid chamel case names like "MyManager"!
     *
     * @param string Last part of the class name
     * @since 2014.03
     */
    /** mshop/catalog/manager/decorators/excludes
     * Excludes decorators added by the "common" option from the catalog manager
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to remove a decorator added via
     * "mshop/common/manager/decorators/default" before they are wrapped
     * around the catalog manager.
     *
     *  mshop/catalog/manager/decorators/excludes = array( 'decorator1' )
     *
     * This would remove the decorator named "decorator1" from the list of
     * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
     * "mshop/common/manager/decorators/default" for the catalog manager.
     *
     * @param array List of decorator names
     * @since 2014.03
     * @see mshop/common/manager/decorators/default
     * @see mshop/catalog/manager/decorators/global
     * @see mshop/catalog/manager/decorators/local
     */
    /** mshop/catalog/manager/decorators/global
     * Adds a list of globally available decorators only to the catalog manager
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap global decorators
     * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the catalog manager.
     *
     *  mshop/catalog/manager/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the catalog
     * manager.
     *
     * @param array List of decorator names
     * @since 2014.03
     * @see mshop/common/manager/decorators/default
     * @see mshop/catalog/manager/decorators/excludes
     * @see mshop/catalog/manager/decorators/local
     */
    /** mshop/catalog/manager/decorators/local
     * Adds a list of local decorators only to the catalog manager
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\MShop\Catalog\Manager\Decorator\*") around the catalog manager.
     *
     *  mshop/catalog/manager/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\MShop\Catalog\Manager\Decorator\Decorator2" only to the catalog
     * manager.
     *
     * @param array List of decorator names
     * @since 2014.03
     * @see mshop/common/manager/decorators/default
     * @see mshop/catalog/manager/decorators/excludes
     * @see mshop/catalog/manager/decorators/global
     */
    private array $search_config = ['id' => ['code' => 'catalog.id', 'internalcode' => 'mcat."id"', 'label' => 'ID', 'type' => 'int', 'public' => false], 'catalog.siteid' => ['code' => 'catalog.siteid', 'internalcode' => 'mcat."siteid"', 'label' => 'Site ID', 'type' => 'string', 'public' => false], 'parentid' => ['code' => 'catalog.parentid', 'internalcode' => 'mcat."parentid"', 'label' => 'Parent ID', 'type' => 'int', 'public' => false], 'level' => ['code' => 'catalog.level', 'internalcode' => 'mcat."level"', 'label' => 'Tree level', 'type' => 'int', 'public' => false], 'left' => ['code' => 'catalog.left', 'internalcode' => 'mcat."nleft"', 'label' => 'Left value', 'type' => 'int', 'public' => false], 'right' => ['code' => 'catalog.right', 'internalcode' => 'mcat."nright"', 'label' => 'Right value', 'type' => 'int', 'public' => false], 'label' => ['code' => 'catalog.label', 'internalcode' => 'mcat."label"', 'label' => 'Label', 'type' => 'string'], 'code' => ['code' => 'catalog.code', 'internalcode' => 'mcat."code"', 'label' => 'Code', 'type' => 'string'], 'status' => ['code' => 'catalog.status', 'internalcode' => 'mcat."status"', 'label' => 'Status', 'type' => 'int'], 'catalog.url' => ['code' => 'catalog.url', 'internalcode' => 'mcat."url"', 'label' => 'URL segment', 'type' => 'string'], 'catalog.pathid' => ['code' => 'catalog.pathid', 'internalcode' => 'mcat."pathid"', 'label' => 'Materialized path', 'type' => 'string', 'public' => false], 'catalog.target' => ['code' => 'catalog.target', 'internalcode' => 'mcat."target"', 'label' => 'URL target', 'type' => 'string'], 'catalog.config' => ['code' => 'catalog.config', 'internalcode' => 'mcat."config"', 'label' => 'Config', 'type' => 'json', 'public' => false], 'catalog.ctime' => ['label' => 'Create date/time', 'code' => 'catalog.ctime', 'internalcode' => 'mcat."ctime"', 'type' => 'datetime', 'public' => false], 'catalog.mtime' => ['label' => 'Modify date/time', 'code' => 'catalog.mtime', 'internalcode' => 'mcat."mtime"', 'type' => 'datetime', 'public' => false], 'catalog.editor' => ['code' => 'catalog.editor', 'internalcode' => 'mcat."editor"', 'label' => 'Editor', 'type' => 'string', 'public' => false], 'catalog:has' => ['code' => 'catalog:has()', 'internalcode' => ':site AND :key AND mcatli."id"', 'internaldeps' => ['LEFT JOIN "mshop_catalog_list" AS mcatli ON ( mcatli."parentid" = mcat."id" )'], 'label' => 'Catalog has list item, parameter(<domain>[,<list type>[,<reference ID>)]]', 'type' => 'null', 'public' => false], 'sort:catalog:position' => ['code' => 'sort:catalog:position', 'internalcode' => 'mcat."nleft"', 'label' => 'Category position', 'type' => 'int', 'public' => false]];
    private array $cache_tags = [];
    /**
     * Initializes the object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context)
    {
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL;
        $level = $context->config()->get('mshop/catalog/manager/sitemode', $level);
        $this->search_config['catalog:has']['function'] = function (&$source, array $params) use ($level): array {
            $keys = [];
            foreach ((array) ($params[1] ?? '') as $type) {
                foreach ((array) ($params[2] ?? '') as $id) {
                    $keys[] = $params[0] . '|' . ($type ? $type . '|' : '') . $id;
                }
            }
            $sitestr = $this->site_string('mcatli."siteid"', $level);
            $keystr = $this->to_expression('mcatli."key"', $keys, $params[2] ?? null ? '==' : '=~');
            $source = str_replace([':site', ':key'], [$sitestr, $keystr], $source);
            return $params;
        };
        parent::__construct($context, $this->search_config);
        /** mshop/catalog/manager/resource
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
        $this->set_resource_name($context->config()->get('mshop/catalog/manager/resource', 'db-catalog'));
    }
    /**
     * Removes old entries from the storage.
     *
     * @param iterable $siteids List of IDs for sites whose entries should be deleted
     * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
     */
    public function clear(iterable $siteids): \Aimeos\M_Shop\Common\Manager\Iface
    {
        $context = $this->context();
        $config = $context->config();
        $search = $this->object()->filter();
        foreach ($config->get('mshop/catalog/manager/submanagers', ['lists']) as $domain) {
            $this->object()->get_sub_manager($domain)->clear($siteids);
        }
        $conn = $context->db($this->get_resource_name());
        /** mshop/catalog/manager/cleanup/mysql
         * Deletes the categories for the given site from the database
         *
         * @see mshop/catalog/manager/cleanup/ansi
         */
        /** mshop/catalog/manager/cleanup/ansi
         * Deletes the categories for the given site from the database
         *
         * Removes the records matched by the given site ID from the catalog
         * database.
         *
         * The ":siteid" placeholder is replaced by the name and value of the
         * site ID column and the given ID or list of IDs.
         *
         * The SQL statement should conform to the ANSI standard to be
         * compatible with most relational database systems. This also
         * includes using double quotes for table and column names.
         *
         * @param string SQL statement for removing the records
         * @since 2014.03
         * @see mshop/catalog/manager/delete/ansi
         * @see mshop/catalog/manager/insert/ansi
         * @see mshop/catalog/manager/update/ansi
         * @see mshop/catalog/manager/newid/ansi
         * @see mshop/catalog/manager/search/ansi
         * @see mshop/catalog/manager/count/ansi
         */
        $path = 'mshop/catalog/manager/cleanup';
        $sql = $this->get_sql_config($path);
        $types = ['siteid' => \Aimeos\Base\DB\Statement\Base::PARAM_STR];
        $translations = ['siteid' => '"siteid"'];
        $search->set_conditions($search->compare('==', 'siteid', $siteids));
        $sql = str_replace(':siteid', $search->get_condition_source($types, $translations), $sql);
        $stmt = $conn->create($sql);
        $stmt->bind(1, 0, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        return $this;
    }
    /**
     * Commits the running database transaction on the connection identified by the given name
     *
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
     */
    public function commit(): \Aimeos\M_Shop\Common\Manager\Iface
    {
        parent::commit();
        $this->context()->cache()->delete_by_tags($this->cache_tags);
        $this->cache_tags = [];
        return $this;
    }
    /**
     * Creates a new empty item instance
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Catalog\Item\Iface New catalog item object
     */
    public function create(array $values = []): \Aimeos\M_Shop\Common\Item\Iface
    {
        $values['siteid'] ??= $this->context()->locale()->get_site_id();
        return $this->create_item_base($values);
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
        return $this->filter_base('catalog', $default);
    }
    /**
     * Removes multiple items.
     *
     * @param \Aimeos\MShop\Common\Item\Iface|array|string $items List of item objects or IDs of the items
     * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
     */
    public function delete($items): \Aimeos\M_Shop\Common\Manager\Iface
    {
        if (is_map($items)) {
            $items = $items->to_array();
        }
        if (!is_array($items)) {
            $items = [$items];
        }
        if (empty($items)) {
            return $this;
        }
        $this->begin();
        $this->lock();
        try {
            $siteid = $this->context()->locale()->get_site_id();
            foreach ($items as $item) {
                $this->create_tree_manager($siteid)->delete_node((string) $item);
            }
            $this->cache_tags = array_merge($this->cache_tags, map($items)->cast()->prefix('catalog-')->all());
            $this->unlock();
            $this->commit();
        } catch (\Exception $e) {
            $this->unlock();
            $this->rollback();
            throw $e;
        }
        return $this->delete_ref_items($items);
    }
    /**
     * Returns the item specified by its code and domain/type if necessary
     *
     * @param string $code Code of the item
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param string|null $domain Domain of the item if necessary to identify the item uniquely
     * @param string|null $type Type code of the item if necessary to identify the item uniquely
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item object
     */
    public function find(string $code, array $ref = [], ?string $domain = null, ?string $type = null, ?bool $default = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->find_base(['catalog.code' => $code], $ref, $default);
    }
    /**
     * Returns the item specified by its ID.
     *
     * @param string $id Unique ID of the catalog item
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item of the given ID
     * @throws \Aimeos\MShop\Exception If item couldn't be found
     */
    public function get(string $id, array $ref = [], ?bool $default = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->get_item_base('catalog.id', $id, $ref, $default);
    }
    /**
     * Returns the attributes that can be used for searching.
     *
     * @param bool $withsub Return also attributes of sub-managers if true
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
     */
    public function get_search_attributes(bool $withsub = true): array
    {
        /** mshop/catalog/manager/submanagers
         * List of manager names that can be instantiated by the catalog manager
         *
         * Managers provide a generic interface to the underlying storage.
         * Each manager has or can have sub-managers caring about particular
         * aspects. Each of these sub-managers can be instantiated by its
         * parent manager using the getSubManager() method.
         *
         * The search keys from sub-managers can be normally used in the
         * manager as well. It allows you to search for items of the manager
         * using the search keys of the sub-managers to further limit the
         * retrieved list of items.
         *
         * @param array List of sub-manager names
         * @since 2014.03
         */
        $path = 'mshop/catalog/manager/submanagers';
        return $this->get_search_attributes_base($this->search_config, $path, [], $withsub);
    }
    /**
     * Adds a new item object.
     *
     * @param \Aimeos\MShop\Catalog\Item\Iface $item Item which should be inserted
     * @param string|null $parentId ID of the parent item where the item should be inserted into
     * @param string|null $refId ID of the item where the item should be inserted before (null to append)
     * @return \Aimeos\MShop\Catalog\Item\Iface $item Updated item including the generated ID
     */
    public function insert(\Aimeos\M_Shop\Catalog\Item\Iface $item, ?string $parent_id = null, ?string $ref_id = null): \Aimeos\M_Shop\Catalog\Item\Iface
    {
        $this->begin();
        $this->lock();
        try {
            $node = $item->get_node();
            $siteid = $this->context()->locale()->get_site_id();
            $manager = $this->create_tree_manager($siteid);
            $manager->insert_node($node, $parent_id, $ref_id);
            $item->set_path_id(join('.', array_keys($manager->get_path($node->get_id()))) . '.');
            $this->update_usage($node->get_id(), $item, true);
            $this->cache_tags[] = 'catalog';
            $this->unlock();
            $this->commit();
        } catch (\Exception $e) {
            $this->unlock();
            $this->rollback();
            throw $e;
        }
        $item = $this->save_list_items($item, 'catalog');
        return $this->save_children($item);
    }
    /**
     * Moves an existing item to the new parent in the storage.
     *
     * @param string $id ID of the item that should be moved
     * @param string|null $oldParentId ID of the old parent item which currently contains the item that should be removed
     * @param string|null $newParentId ID of the new parent item where the item should be moved to
     * @param string|null $refId ID of the item where the item should be inserted before (null to append)
     * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
     */
    public function move(string $id, ?string $old_parent_id = null, ?string $new_parent_id = null, ?string $ref_id = null): \Aimeos\M_Shop\Catalog\Manager\Iface
    {
        $this->begin();
        $this->lock();
        try {
            $item = $this->object()->get($id);
            $siteid = $this->context()->locale()->get_site_id();
            $manager = $this->create_tree_manager($siteid);
            $manager->move_node($id, $old_parent_id, $new_parent_id, $ref_id);
            $this->update_paths($manager, $id);
            $this->update_usage($id, $item);
            $this->cache_tags[] = 'catalog';
            $this->unlock();
            $this->commit();
        } catch (\Exception $e) {
            $this->unlock();
            $this->rollback();
            throw $e;
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
        $items = parent::save($items, $fetch);
        $this->cache_tags = array_merge($this->cache_tags, map($items)->get_id()->prefix('catalog-')->all());
        return $items;
    }
    /**
     * Updates an item object.
     *
     * @param \Aimeos\MShop\Catalog\Item\Iface $item Item object whose data should be saved
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Catalog\Item\Iface $item Updated item including the generated ID
     */
    protected function save_item(\Aimeos\M_Shop\Catalog\Item\Iface $item, bool $fetch = true): \Aimeos\M_Shop\Catalog\Item\Iface
    {
        if (!$item->is_modified()) {
            $item = $this->save_list_items($item, 'catalog', $fetch);
            return $this->save_children($item);
        }
        $node = $item->get_node();
        $siteid = $this->context()->locale()->get_site_id();
        $this->create_tree_manager($siteid)->save_node($node);
        $this->update_usage($node->get_id(), $item);
        $item = $this->save_list_items($item, 'catalog', $fetch);
        return $this->save_children($item);
    }
    /**
     * Searches for all items matching the given critera.
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param int|null &$total Number of items that are available in total
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Catalog\Item\Iface with ids as keys
     */
    public function search(\Aimeos\Base\Criteria\Iface $search, array $ref = [], ?int &$total = null): \Aimeos\Map
    {
        $map = [];
        $required = ['catalog'];
        $context = $this->context();
        $conn = $context->db($this->get_resource_name());
        /** mshop/catalog/manager/sitemode
         * Mode how items from levels below or above in the site tree are handled
         *
         * By default, only items from the current site are fetched from the
         * storage. If the ai-sites extension is installed, you can create a
         * tree of sites. Then, this setting allows you to define for the
         * whole catalog domain if items from parent sites are inherited,
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
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_PATH;
        $level = $context->config()->get('mshop/catalog/manager/sitemode', $level);
        /** mshop/catalog/manager/search-item/mysql
         * Retrieves the records matched by the given criteria in the database
         *
         * @see mshop/catalog/manager/search-item/ansi
         */
        /** mshop/catalog/manager/search-item/ansi
         * Retrieves the records matched by the given criteria in the database
         *
         * Fetches the records matched by the given criteria from the catalog
         * database. The records must be from one of the sites that are
         * configured via the context item. If the current site is part of
         * a tree of sites, the SELECT statement can retrieve all records
         * from the current site and the complete sub-tree of sites.
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
         * If the records that are retrieved should be ordered by one or more
         * columns, the generated string of column / sort direction pairs
         * replaces the ":order" placeholder. Columns of
         * sub-managers can also be used for ordering the result set but then
         * no index can be used.
         *
         * The number of returned records can be limited and can start at any
         * number between the begining and the end of the result set. For that
         * the ":size" and ":start" placeholders are replaced by the
         * corresponding values from the criteria object. The default values
         * are 0 for the start and 100 for the size value.
         *
         * The SQL statement should conform to the ANSI standard to be
         * compatible with most relational database systems. This also
         * includes using double quotes for table and column names.
         *
         * @param string SQL statement for searching items
         * @since 2014.03
         * @see mshop/catalog/manager/delete/ansi
         * @see mshop/catalog/manager/get/ansi
         * @see mshop/catalog/manager/insert/ansi
         * @see mshop/catalog/manager/update/ansi
         * @see mshop/catalog/manager/newid/ansi
         * @see mshop/catalog/manager/search/ansi
         * @see mshop/catalog/manager/count/ansi
         * @see mshop/catalog/manager/move-left/ansi
         * @see mshop/catalog/manager/move-right/ansi
         * @see mshop/catalog/manager/update-parentid/ansi
         */
        $cfg_path_search = 'mshop/catalog/manager/search-item';
        /** mshop/catalog/manager/count/mysql
         * Counts the number of records matched by the given criteria in the database
         *
         * @see mshop/catalog/manager/count/ansi
         */
        /** mshop/catalog/manager/count/ansi
         * Counts the number of records matched by the given criteria in the database
         *
         * Counts all records matched by the given criteria from the catalog
         * database. The records must be from one of the sites that are
         * configured via the context item. If the current site is part of
         * a tree of sites, the statement can count all records from the
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
         * Both, the strings for ":joins" and for ":cond" are the same as for
         * the "search" SQL statement.
         *
         * Contrary to the "search" statement, it doesn't return any records
         * but instead the number of records that have been found. As counting
         * thousands of records can be a long running task, the maximum number
         * of counted records is limited for performance reasons.
         *
         * The SQL statement should conform to the ANSI standard to be
         * compatible with most relational database systems. This also
         * includes using double quotes for table and column names.
         *
         * @param string SQL statement for counting items
         * @since 2014.03
         * @see mshop/catalog/manager/delete/ansi
         * @see mshop/catalog/manager/get/ansi
         * @see mshop/catalog/manager/insert/ansi
         * @see mshop/catalog/manager/update/ansi
         * @see mshop/catalog/manager/newid/ansi
         * @see mshop/catalog/manager/search/ansi
         * @see mshop/catalog/manager/search-item/ansi
         * @see mshop/catalog/manager/move-left/ansi
         * @see mshop/catalog/manager/move-right/ansi
         * @see mshop/catalog/manager/update-parentid/ansi
         */
        $cfg_path_count = 'mshop/catalog/manager/count';
        if ($search->get_sortations() === []) {
            $search->set_sortations([$search->sort('+', 'sort:catalog:position')]);
        }
        $results = $this->search_items_base($conn, $search, $cfg_path_search, $cfg_path_count, $required, $total, $level);
        while ($row = $results->fetch()) {
            $map[$row['id']] = new \Aimeos\MW\Tree\Node\Db_Nested_Set($row);
        }
        return $this->build_items($map, $ref, 'catalog');
    }
    /**
     * Returns a list of items starting with the given category that are in the path to the root node
     *
     * @param string $id ID of item to get the path for
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @return \Aimeos\Map Associative list of catalog items implementing \Aimeos\MShop\Catalog\Item\Iface with IDs as keys
     */
    public function get_path(string $id, array $ref = []): \Aimeos\Map
    {
        $mode = \Aimeos\M_Shop\Locale\Manager\Base::SITE_PATH;
        $mode = $this->context()->config()->get('mshop/catalog/manager/sitemode', $mode);
        if ($mode !== \Aimeos\M_Shop\Locale\Manager\Base::SITE_ONE) {
            $site_path = array_reverse($this->context()->locale()->get_site_path());
        } else {
            $site_path = [$this->context()->locale()->get_site_id()];
        }
        foreach ($site_path as $site_id) {
            try {
                $path = $this->create_tree_manager($site_id)->get_path($id);
            } catch (\Exception) {
                continue;
            }
            if (!empty($path)) {
                $item_map = [];
                foreach ($path as $node) {
                    $item_map[$node->get_id()] = $node;
                }
                return $this->build_items($item_map, $ref, 'catalog');
            }
        }
        $msg = $this->context()->translate('mshop', 'Catalog path for ID "%1$s" not found');
        throw new \Aimeos\M_Shop\Catalog\Exception(sprintf($msg, $id), 404);
    }
    /**
     * Returns a node and its descendants depending on the given resource.
     *
     * @param string|null $id Retrieve nodes starting from the given ID
     * @param string[] List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
     * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
     * @param \Aimeos\Base\Criteria\Iface|null $criteria Optional criteria object with conditions
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item, maybe with subnodes
     */
    public function get_tree(?string $id = null, array $ref = [], int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE, ?\Aimeos\Base\Criteria\Iface $criteria = null): \Aimeos\M_Shop\Catalog\Item\Iface
    {
        $mode = \Aimeos\M_Shop\Locale\Manager\Base::SITE_PATH;
        $mode = $this->context()->config()->get('mshop/catalog/manager/sitemode', $mode);
        if ($mode === \Aimeos\M_Shop\Locale\Manager\Base::SITE_PATH) {
            $site_path = array_reverse($this->context()->locale()->get_site_path());
        } else {
            $site_path = [$this->context()->locale()->get_site_id()];
        }
        foreach ($site_path as $site_id) {
            try {
                $node = $this->create_tree_manager($site_id)->get_node($id, $level, $criteria);
            } catch (\Aimeos\MW\Tree\Exception) {
                continue;
            }
            $list_items = [];
            $nodeid = $node->get_id();
            $node_map = $this->get_node_map($node);
            if (!empty($ref)) {
                $list_items = map($this->get_list_items(array_keys($node_map), $ref, 'catalog'))->group_by('catalog.lists.parentid')->all();
            }
            if ($item = $this->apply_filter($this->create_item_base([], $list_items[$nodeid] ?? [], [], [], $node))) {
                $this->create_tree($node, $item, $list_items, []);
                return $item;
            }
        }
        $msg = $this->context()->translate('mshop', 'No catalog node for ID "%1$s"');
        throw new \Aimeos\M_Shop\Catalog\Exception(sprintf($msg, $id), 404);
    }
    /**
     * Creates a new extension manager in the domain.
     *
     * @param string $manager Name of the sub manager type
     * @param string|null $name Name of the implementation, will be from configuration (or Default)
     * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
     */
    public function get_sub_manager(string $manager, ?string $name = null): \Aimeos\M_Shop\Common\Manager\Iface
    {
        return $this->get_sub_manager_base('catalog', $manager, $name);
    }
    /**
     * Saves the children of the given node
     *
     * @param \Aimeos\MShop\Catalog\Item\Iface $item Catalog item object incl. child items
     * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item with saved child items
     */
    protected function save_children(\Aimeos\M_Shop\Catalog\Item\Iface $item): \Aimeos\M_Shop\Catalog\Item\Iface
    {
        $rm_ids = [];
        foreach ($item->get_children_deleted() as $child) {
            $rm_ids[] = $child->get_id();
        }
        $this->delete($rm_ids);
        foreach ($item->get_children() as $child) {
            if ($child->get_id() !== null) {
                $this->save($child);
                if ($child->get_parent_id() !== $item->get_id()) {
                    $this->move($child->get_id(), $item->get_parent_id(), $child->get_parent_id());
                }
            } else {
                $this->insert($child, $item->get_id());
            }
        }
        return $item;
    }
    /**
     * Locks the catalog table against modifications from other connections
     *
     * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
     */
    protected function lock(): \Aimeos\M_Shop\Catalog\Manager\Iface
    {
        /** mshop/catalog/manager/lock/mysql
         * SQL statement for locking the catalog table
         *
         * @see mshop/catalog/manager/lock/ansi
         */
        /** mshop/catalog/manager/lock/ansi
         * SQL statement for locking the catalog table
         *
         * Updating the nested set of categories in the catalog table requires locking
         * the whole table to avoid data corruption. This statement will be followed by
         * insert or update statements and closed by an unlock statement.
         *
         * @param string Lock SQL statement
         * @since 2019.04
         */
        $path = 'mshop/catalog/manager/lock';
        if (($sql = $this->get_sql_config($path)) !== $path) {
            $conn = $this->context()->db($this->get_resource_name());
            $conn->create($sql)->execute()->finish();
        }
        return $this;
    }
    /**
     * Unlocks the catalog table for modifications from other connections
     *
     * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
     */
    protected function unlock(): \Aimeos\M_Shop\Catalog\Manager\Iface
    {
        /** mshop/catalog/manager/unlock/mysql
         * SQL statement for unlocking the catalog table
         *
         * @see mshop/catalog/manager/unlock/ansi
         */
        /** mshop/catalog/manager/unlock/ansi
         * SQL statement for unlocking the catalog table
         *
         * Updating the nested set of categories in the catalog table requires locking
         * the whole table to avoid data corruption. This statement will be executed
         * after the table is locked and insert or update statements have been sent to
         * the database.
         *
         * @param string Lock SQL statement
         * @since 2019.04
         */
        $path = 'mshop/catalog/manager/unlock';
        if (($sql = $this->get_sql_config($path)) !== $path) {
            $conn = $this->context()->db($this->get_resource_name());
            $conn->create($sql)->execute()->finish();
        }
        return $this;
    }
    /**
     * Updates the materialized paths for a node and all its descendants.
     *
     * @param \Aimeos\MW\Tree\Manager\Iface $manager Tree manager
     * @param string $id ID of the moved node
     * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
     */
    private function update_paths(\Aimeos\MW\Tree\Manager\Iface $manager, string $id): \Aimeos\M_Shop\Catalog\Manager\Iface
    {
        $context = $this->context();
        $conn = $context->db($this->get_resource_name());
        $node = $manager->get_node($id, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE);
        $this->update_paths_recursive($conn, $manager, $node);
        return $this;
    }
    /**
     * Recursively updates the materialized path for a node and its children.
     *
     * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
     * @param \Aimeos\MW\Tree\Manager\Iface $manager Tree manager
     * @param \Aimeos\MW\Tree\Node\Iface $node Tree node
     */
    private function update_paths_recursive(\Aimeos\Base\DB\Connection\Iface $conn, \Aimeos\MW\Tree\Manager\Iface $manager, \Aimeos\MW\Tree\Node\Iface $node): void
    {
        $path = join('.', array_keys($manager->get_path($node->get_id()))) . '.';
        /** mshop/catalog/manager/update-path/ansi
         * Updates the materialized path of a catalog node
         *
         * @param string SQL statement for updating the path
         * @since 2026.04
         */
        $sql = $this->get_sql_config('mshop/catalog/manager/update-path');
        $stmt = $conn->create($sql);
        $stmt->bind(1, $path);
        $stmt->bind(2, (int) $node->get_id(), \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        foreach ($node->get_children() as $child) {
            $this->update_paths_recursive($conn, $manager, $child);
        }
    }
    /**
     * Updates the usage information of a node.
     *
     * @param string $id Id of the record
     * @param \Aimeos\MShop\Catalog\Item\Iface $item Catalog item
     * @param bool $case True if the record should be added or false for an update
     * @return \Aimeos\MShop\Catalog\Manager\Iface Manager object for chaining method calls
     */
    private function update_usage(string $id, \Aimeos\M_Shop\Catalog\Item\Iface $item, bool $case = false): \Aimeos\M_Shop\Catalog\Manager\Iface
    {
        $context = $this->context();
        $conn = $context->db($this->get_resource_name());
        $siteid = $context->locale()->get_site_id();
        $columns = $this->object()->get_save_attributes();
        if ($case !== true) {
            /** mshop/catalog/manager/update-usage/mysql
             * Updates the config, editor and mtime value of an updated record
             *
             * @see mshop/catalog/manager/update-usage/ansi
             */
            /** mshop/catalog/manager/update-usage/ansi
             * Updates the config, editor and mtime value of an updated record
             *
             * Each record contains some usage information like when it was
             * created, last modified and by whom. These information are part
             * of the catalog items and the generic tree manager doesn't care
             * about this information. Thus, they are updated after the tree
             * manager saved the basic record information.
             *
             * The SQL statement must be a string suitable for being used as
             * prepared statement. It must include question marks for binding
             * the values from the catalog item to the statement before they are
             * sent to the database server. The order of the columns must
             * correspond to the order in the method using this statement,
             * so the correct values are bound to the columns.
             *
             * The SQL statement should conform to the ANSI standard to be
             * compatible with most relational database systems. This also
             * includes using double quotes for table and column names.
             *
             * @param string SQL statement for updating records
             * @since 2014.03
             * @see mshop/catalog/manager/delete/ansi
             * @see mshop/catalog/manager/get/ansi
             * @see mshop/catalog/manager/insert/ansi
             * @see mshop/catalog/manager/newid/ansi
             * @see mshop/catalog/manager/search/ansi
             * @see mshop/catalog/manager/search-item/ansi
             * @see mshop/catalog/manager/count/ansi
             * @see mshop/catalog/manager/move-left/ansi
             * @see mshop/catalog/manager/move-right/ansi
             * @see mshop/catalog/manager/update-parentid/ansi
             * @see mshop/catalog/manager/insert-usage/ansi
             */
            $path = 'mshop/catalog/manager/update-usage';
        } else {
            /** mshop/catalog/manager/insert-usage/mysql
             * Updates the config, editor, ctime and mtime value of an inserted record
             *
             * @see mshop/catalog/manager/insert-usage/ansi
             */
            /** mshop/catalog/manager/insert-usage/ansi
             * Updates the config, editor, ctime and mtime value of an inserted record
             *
             * Each record contains some usage information like when it was
             * created, last modified and by whom. These information are part
             * of the catalog items and the generic tree manager doesn't care
             * about this information. Thus, they are updated after the tree
             * manager inserted the basic record information.
             *
             * The SQL statement must be a string suitable for being used as
             * prepared statement. It must include question marks for binding
             * the values from the catalog item to the statement before they are
             * sent to the database server. The order of the columns must
             * correspond to the order in the method using this statement,
             * so the correct values are bound to the columns.
             *
             * The SQL statement should conform to the ANSI standard to be
             * compatible with most relational database systems. This also
             * includes using double quotes for table and column names.
             *
             * @param string SQL statement for updating records
             * @since 2014.03
             * @see mshop/catalog/manager/delete/ansi
             * @see mshop/catalog/manager/get/ansi
             * @see mshop/catalog/manager/insert/ansi
             * @see mshop/catalog/manager/newid/ansi
             * @see mshop/catalog/manager/search/ansi
             * @see mshop/catalog/manager/search-item/ansi
             * @see mshop/catalog/manager/count/ansi
             * @see mshop/catalog/manager/move-left/ansi
             * @see mshop/catalog/manager/move-right/ansi
             * @see mshop/catalog/manager/update-parentid/ansi
             * @see mshop/catalog/manager/update-usage/ansi
             */
            $path = 'mshop/catalog/manager/insert-usage';
        }
        $sql = $this->add_sql_columns(array_keys($columns), $this->get_sql_config($path), false);
        $stmt = $this->get_cached_statement($conn, $path, $sql);
        $idx = 1;
        foreach ($columns as $name => $entry) {
            $stmt->bind($idx++, $item->get($name), \Aimeos\Base\Criteria\SQL::type($entry->get_type()));
        }
        $stmt->bind($idx++, $item->get_url());
        $stmt->bind($idx++, json_encode($item->get_config(), JSON_FORCE_OBJECT));
        $stmt->bind($idx++, $item->get_path_id());
        $stmt->bind($idx++, $context->datetime());
        // mtime
        $stmt->bind($idx++, $context->editor());
        $stmt->bind($idx++, $item->get_target());
        if ($case !== true) {
            $stmt->bind($idx++, $siteid);
            $stmt->bind($idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        } else {
            $stmt->bind($idx++, $context->datetime());
            // ctime
            $stmt->bind($idx++, $siteid);
            $stmt->bind($idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        }
        $stmt->execute()->finish();
        return $this;
    }
}