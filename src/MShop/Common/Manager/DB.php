<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager;

/**
 * Method trait for managers
 *
 * @package MShop
 * @subpackage Common
 */
trait DB
{
    private ?\Aimeos\Base\Criteria\Iface $search;
    private ?string $resource_name = null;
    private array $cached_stmts = [];
    private string $subpath;
    private string $domain;
    /**
     * Returns the context object.
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    abstract protected function context(): \Aimeos\M_Shop\Context_Iface;
    /**
     * Creates the criteria attribute items from the list of entries
     *
     * @param array $list Associative array of code as key and array with properties as values
     * @return \Aimeos\Base\Criteria\Attribute\Standard[] List of criteria attribute items
     */
    abstract protected function create_attributes(array $list): array;
    /**
     * Returns the attribute helper functions for searching defined by the manager.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
     * @return array Associative array of attribute code and helper function
     */
    abstract protected function get_search_functions(array $attributes): array;
    /**
     * Returns the attribute translations for searching defined by the manager.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
     * @return array Associative array of attribute code and internal attribute code
     */
    abstract protected function get_search_translations(array $attributes): array;
    /**
     * Returns the attribute types for searching defined by the manager.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
     * @return array Associative array of attribute code and internal attribute type
     */
    abstract protected function get_search_types(array $attributes): array;
    /**
     * Returns the outmost decorator of the decorator stack
     *
     * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
     */
    abstract protected function object(): \Aimeos\M_Shop\Common\Manager\Iface;
    /**
     * Returns the site expression for the given name
     *
     * @param string $name Name of the site condition
     * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
     * @return \Aimeos\Base\Criteria\Expression\Iface Site search condition
     */
    abstract protected function site_condition(string $name, int $sitelevel): \Aimeos\Base\Criteria\Expression\Iface;
    /**
     * Returns the site ID that should be used based on the site level
     *
     * @param string $siteId Site ID to check
     * @param int $sitelevel Site level to check against
     * @return string Site ID that should be use based on the site level
     * @since 2022.04
     */
    abstract protected function site_id(string $site_id, int $sitelevel): string;
    /**
     * Returns the type of the mananger as separate parts
     *
     * @return string[] List of manager part names
     */
    abstract public function type(): array;
    /**
     * Adds additional column names to SQL statement
     *
     * @param string[] $columns List of column names
     * @param string $sql Insert or update SQL statement
     * @param bool $mode True for insert, false for update statement
     * @return string Modified insert or update SQL statement
     */
    protected function add_sql_columns(array $columns, string $sql, bool $mode = true): string
    {
        $names = $values = '';
        if ($mode) {
            foreach ($columns as $name) {
                $names .= '"' . $name . '", ';
                $values .= '?, ';
            }
        } else {
            foreach ($columns as $name) {
                $names .= '"' . $name . '" = ?, ';
            }
        }
        return str_replace([':names', ':values'], [$names, $values], $sql);
    }
    /**
     * Counts the number products that are available for the values of the given key.
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search criteria
     * @param array|string $keys Search key or list of keys for aggregation
     * @param string $cfgPath Configuration key for the SQL statement
     * @param string[] $required List of domain/sub-domain names like "catalog.index" that must be additionally joined
     * @param string|null $value Search key for aggregating the value column
     * @param string|null $type Type of aggregation, e.g.  "sum", "min", "max" or NULL for "count"
     * @return \Aimeos\Map List of ID values as key and the number of counted products as value
     */
    protected function aggregate_base(\Aimeos\Base\Criteria\Iface $search, $keys, string $cfg_path, array $required = [], ?string $value = null, ?string $type = null): \Aimeos\Map
    {
        /** mshop/common/manager/aggregate/limit
         * Limits the number of records that are used when aggregating items
         *
         * As counting huge amount of records (several 10 000 records) takes a long time,
         * the limit can cut down response times so the counts are available more quickly
         * in the front-end and the server load is reduced.
         *
         * Using a low limit can lead to incorrect numbers if the amount of found items
         * is very high. Approximate item counts are normally not a problem but it can
         * lead to the situation that visitors see that no items are available despite
         * the fact that there would be at least one.
         *
         * @param integer Number of records
         * @since 2021.04
         */
        $limit = $this->context()->config()->get('mshop/common/manager/aggregate/limit', 10000);
        if (empty($keys)) {
            $msg = $this->context()->translate('mshop', 'At least one key is required for aggregation');
            throw new \Aimeos\M_Shop\Exception($msg);
        }
        $attr_map = array_column(array_filter($this->object()->get_search_attributes(), fn($item) => $item->is_public() || str_starts_with($item->get_code(), 'agg:')), null, 'code');
        if ($value === null && ($value = key($attr_map)) === null) {
            $msg = $this->context()->translate('mshop', 'No search keys available');
            throw new \Aimeos\M_Shop\Exception($msg);
        }
        if (($pos = strpos($valkey = $value, '(')) !== false) {
            $value = substr($value, 0, $pos) . '()';
            // remove parameters from search function
        }
        if (!isset($attr_map[$value])) {
            $msg = $this->context()->translate('mshop', 'Unknown search key "%1$s"');
            throw new \Aimeos\M_Shop\Exception(sprintf($msg, $value));
        }
        $keys = (array) $keys;
        $acols = $cols = $expr = [];
        $search = (clone $search)->slice($search->get_offset(), min($search->get_limit(), $limit));
        foreach ($keys as $string) {
            if (($attr_item = $attr_map[$string] ?? null) === null) {
                $msg = $this->context()->translate('mshop', 'Unknown search key "%1$s"');
                throw new \Aimeos\M_Shop\Exception(sprintf($msg, $string));
            }
            if (!str_contains($attr_item->get_internal_code(), '"')) {
                $prefixed = $this->alias($attr_item->get_code()) . '."' . $attr_item->get_internal_code() . '"';
            } else {
                // @todo: Remove in 2025.01
                $prefixed = $attr_item->get_internal_code();
            }
            $acols[] = $prefixed . ' AS "' . $string . '"';
            $cols[] = $prefixed;
            $expr[] = $search->compare('!=', $string, null);
            // required for the joins
        }
        $expr[] = $search->compare('!=', $valkey, null);
        $search->add($search->and($expr));
        $val = $attr_map[$value]->get_internal_code();
        if (!str_contains($val, '"')) {
            $val = $this->alias($attr_map[$value]->get_code()) . '."' . $val . '"';
        }
        $sql = $this->get_sql_config($cfg_path);
        $sql = str_replace(':cols', join(', ', $cols), $sql);
        $sql = str_replace(':acols', join(', ', $acols), $sql);
        $sql = str_replace(':keys', '"' . join('", "', $keys) . '"', $sql);
        $sql = str_replace(':type', in_array($type, ['avg', 'count', 'max', 'min', 'sum']) ? $type : 'count', $sql);
        $sql = str_replace(':val', $val, $sql);
        return $this->aggregate_result($search, $sql, $required);
    }
    /**
     * Returns the aggregated values for the given SQL string and filter.
     *
     * @param \Aimeos\Base\Criteria\Iface $filter Filter object
     * @param string $sql SQL statement
     * @param string[] $required List of domain/sub-domain names like "catalog.index" that must be additionally joined
     * @return \Aimeos\Map (Nested) list of aggregated values as key and the number of counted products as value
     */
    protected function aggregate_result(\Aimeos\Base\Criteria\Iface $filter, string $sql, array $required): \Aimeos\Map
    {
        $map = [];
        $total = null;
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL;
        $conn = $this->context()->db($this->get_resource_name());
        $results = $this->search_items_base($conn, $filter, $sql, '', $required, $total, $level);
        while ($row = $results->fetch()) {
            $row = $this->transform($row);
            $temp =& $map;
            $last = array_pop($row);
            foreach ($row as $val) {
                $temp[$val] ??= [];
                $temp =& $temp[$val];
            }
            $temp = $last;
        }
        return map($map);
    }
    /**
     * Returns the table alias name.
     *
     * @param string|null $attrcode Search attribute code
     * @return string Table alias name
     */
    protected function alias(?string $attrcode = null): string
    {
        if ($attrcode) {
            $parts = array_slice(explode('.', $attrcode), 0, -1) ?: $this->type();
            $str = 'm' . substr((string) array_shift($parts), 0, 3);
        } else {
            $parts = $this->type();
            $str = 'm' . substr((string) array_shift($parts), 0, 3);
        }
        foreach ($parts as $part) {
            $str .= substr($part, 0, 2);
        }
        return $str;
    }
    /**
     * Adds aliases for the columns
     *
     * @param array $map Associative list of search keys as keys and internal column names as values
     * @return array Associative list of search keys as keys and aliased column names as values
     */
    protected function alias_translations(array $map): array
    {
        foreach ($map as $key => $value) {
            if (!str_contains($value, '"')) {
                $map[$key] = $this->alias($key) . '."' . $value . '"';
            }
        }
        return $map;
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
        return $stmt;
    }
    /**
     * Removes old entries from the storage.
     *
     * @param iterable $siteids List of IDs for sites whose entries should be deleted
     * @param string $cfgpath Configuration key to the cleanup statement
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
     */
    protected function clear_base(iterable $siteids, string $cfgpath): \Aimeos\M_Shop\Common\Manager\Iface
    {
        if (empty($siteids)) {
            return $this;
        }
        $conn = $this->context()->db($this->get_resource_name());
        $sql = $this->get_sql_config($cfgpath);
        $sql = str_replace(':cond', '1=1', $sql);
        $stmt = $conn->create($sql);
        foreach ($siteids as $siteid) {
            $stmt->bind(1, $siteid);
            $stmt->execute()->finish();
        }
        return $this;
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
        }
        $stmt->execute()->finish();
        return $this;
    }
    /**
     * Sets the base criteria "status".
     * (setConditions overwrites the base criteria)
     *
     * @param string $domain Name of the domain/sub-domain like "product" or "product.list"
     * @param bool|null $default TRUE for status=1, NULL for status>0, FALSE for no restriction
     * @return \Aimeos\Base\Criteria\Iface Search critery object
     */
    protected function filter_base(string $domain, ?bool $default = false): \Aimeos\Base\Criteria\Iface
    {
        $context = $this->context();
        $db = $this->get_resource_name();
        $conn = $context->db($db);
        $config = $context->config();
        if (($adapter = $config->get('resource/' . $db . '/adapter')) === null) {
            $adapter = $config->get('resource/db/adapter');
        }
        $filter = match ($adapter) {
            'pgsql' => new \Aimeos\Base\Criteria\Pg_Sql($conn),
            default => new \Aimeos\Base\Criteria\SQL($conn),
        };
        if ($default !== false) {
            $filter->add($domain . '.status', $default ? '==' : '>=', 1);
        }
        return $filter;
    }
    /**
     * Returns the item for the given search key/value pairs.
     *
     * @param array $pairs Search key/value pairs for the item
     * @param string[] $ref List of domains whose items should be fetched too
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Common\Item\Iface Requested item
     * @throws \Aimeos\MShop\Exception if no item with the given ID found
     */
    protected function find_base(array $pairs, array $ref, ?bool $default): \Aimeos\M_Shop\Common\Item\Iface
    {
        $expr = [];
        $criteria = $this->object()->filter($default)->slice(0, 1);
        foreach ($pairs as $key => $value) {
            if ($value === null) {
                $msg = $this->context()->translate('mshop', 'Required value for "%1$s" is missing');
                throw new \Aimeos\M_Shop\Exception(sprintf($msg, $key));
            }
            $expr[] = $criteria->compare('==', $key, $value);
        }
        $criteria->set_conditions($criteria->and($expr));
        if ($item = $this->object()->search($criteria, $ref)->first()) {
            return $item;
        }
        $msg = $this->context()->translate('mshop', 'No item found for conditions: %1$s');
        throw new \Aimeos\M_Shop\Exception(sprintf($msg, print_r($pairs, true)), 404);
    }
    /**
     * Returns the cached statement for the given key or creates a new prepared statement.
     * If no SQL string is given, the key is used to retrieve the SQL string from the configuration.
     *
     * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
     * @param string $cfgkey Unique key for the SQL
     * @param string|null $sql SQL string if it shouldn't be retrieved from the configuration
     * @return \Aimeos\Base\DB\Statement\Iface Database statement object
     */
    protected function get_cached_statement(\Aimeos\Base\DB\Connection\Iface $conn, string $cfgkey, ?string $sql = null): \Aimeos\Base\DB\Statement\Iface
    {
        if (!isset($this->cached_stmts['stmt'][$cfgkey]) || !isset($this->cached_stmts['conn'][$cfgkey]) || $conn !== $this->cached_stmts['conn'][$cfgkey]) {
            if ($sql === null) {
                $sql = $this->get_sql_config($cfgkey);
            }
            $this->cached_stmts['stmt'][$cfgkey] = $conn->create($sql);
            $this->cached_stmts['conn'][$cfgkey] = $conn;
        }
        return $this->cached_stmts['stmt'][$cfgkey];
    }
    /**
     * Returns the full configuration key for the passed last part
     *
     * @param string $name Configuration last part
     * @param string $default Default configuration key
     * @return string Full configuration key
     */
    protected function get_config_key(string $name, string $default = ''): string
    {
        $type = $this->type();
        array_splice($type, 1, 0, ['manager']);
        $key = 'mshop/' . join('/', $type) . '/' . $name;
        if ($this->context()->config()->get($key)) {
            return $key;
        }
        return $default;
    }
    /**
     * Returns a sorted list of required criteria keys.
     *
     * @param \Aimeos\Base\Criteria\Iface $criteria Search criteria object
     * @param string[] $required List of prefixes of required search conditions
     * @return string[] Sorted list of criteria keys
     */
    protected function get_criteria_key_list(\Aimeos\Base\Criteria\Iface $criteria, array $required): array
    {
        $keys = array_merge($required, $this->get_criteria_keys($required, $criteria->get_conditions()));
        foreach ($criteria->get_sortations() as $sortation) {
            $keys = array_merge($keys, $this->get_criteria_keys($required, $sortation));
        }
        $keys = array_unique(array_merge($required, $keys));
        sort($keys);
        return $keys;
    }
    /**
     * Returns the item for the given search key and ID.
     *
     * @param string $key Search key for the requested ID
     * @param string $id Unique ID to search for
     * @param string[] $ref List of domains whose items should be fetched too
     * @param bool|null $default Add default criteria or NULL for relaxed default criteria
     * @return \Aimeos\MShop\Common\Item\Iface Requested item
     * @throws \Aimeos\MShop\Exception if no item with the given ID found
     */
    protected function get_item_base(string $key, string $id, array $ref, ?bool $default): \Aimeos\M_Shop\Common\Item\Iface
    {
        $criteria = $this->object()->filter($default)->add([$key => $id])->slice(0, 1);
        if ($item = $this->object()->search($criteria, $ref)->first()) {
            return $item;
        }
        $msg = $this->context()->translate('mshop', 'Item with ID "%2$s" in "%1$s" not found');
        throw new \Aimeos\M_Shop\Exception(sprintf($msg, $key, $id), 404);
    }
    /**
     * Returns the SQL strings for joining dependent tables.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of criteria attribute items
     * @param string $prefix Search key prefix
     * @return array List of JOIN SQL strings
     */
    protected function get_joins(array $attributes, string $prefix): array
    {
        $iface = \Aimeos\Base\Criteria\Attribute\Iface::class;
        $name = $prefix . '.id';
        if (isset($attributes[$prefix]) && $attributes[$prefix] instanceof $iface) {
            return $attributes[$prefix]->get_internal_deps();
        }
        if (isset($attributes[$name]) && $attributes[$name] instanceof $iface) {
            return $attributes[$name]->get_internal_deps();
        }
        if (isset($attributes['id']) && $attributes['id'] instanceof $iface) {
            return $attributes['id']->get_internal_deps();
        }
        return [];
    }
    /**
     * Returns the required SQL joins for the critera.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of criteria attribute items
     * @param string $prefix Search key prefix
     * @return array|null List of JOIN SQL strings
     */
    protected function get_required_joins(array $attributes, array $keys, ?string $basekey = null): array
    {
        $joins = [];
        foreach ($keys as $key) {
            if ($key !== $basekey) {
                $joins = array_merge($joins, $this->get_joins($attributes, $key));
            }
        }
        return array_unique($joins);
    }
    /**
     * Returns the name of the resource.
     *
     * @return string Name of the resource, e.g. "db-product"
     */
    protected function get_resource_name(): string
    {
        if ($this->resource_name === null) {
            $this->set_resource_name('db-' . current($this->type()));
        }
        return $this->resource_name;
    }
    /**
     * Returns the search attribute objects used for searching.
     *
     * @param array $list Associative list of search keys and the lists of search definitions
     * @param string $path Configuration path to the sub-domains for fetching the search definitions
     * @param string[] $default List of sub-domains if no others are configured
     * @param bool $withsub True to include search definitions of sub-domains, false if not
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] Associative list of search keys and criteria attribute items as values
     * @since 2014.09
     */
    protected function get_search_attributes_base(array $list, string $path, array $default, bool $withsub): array
    {
        $attr = $this->create_attributes($list);
        if ($withsub === true) {
            $config = $this->context()->config();
            $domains = $config->get($path, $default);
            foreach ($domains as $domain) {
                $name = $config->get(substr($path, 0, strrpos($path, '/')) . '/' . $domain . '/name');
                $attr += $this->object()->get_sub_manager($domain, $name)->get_search_attributes(true);
            }
        }
        return $attr;
    }
    /**
     * Returns the item search key for the passed name
     *
     * @param string $name Search key name
     * @return string Item prefix e.g. "product.lists.type.id"
     */
    protected function get_search_key(string $name = ''): string
    {
        return join('.', $this->type()) . ($name ? '.' . $name : '');
    }
    /**
     * Returns the search results for the given SQL statement.
     *
     * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
     * @param string $sql SQL statement
     * @return \Aimeos\Base\DB\Result\Iface Search result object
     */
    protected function get_search_results(\Aimeos\Base\DB\Connection\Iface $conn, string $sql): \Aimeos\Base\DB\Result\Iface
    {
        $time = microtime(true);
        $stmt = $conn->create($sql);
        $result = $stmt->execute();
        $level = \Aimeos\Base\Logger\Iface::DEBUG;
        $time = (microtime(true) - $time) * 1000;
        $msg = 'Time: ' . $time . "ms\n" . 'Class: ' . $this::class . "\n" . str_replace(["\t", "\n\n"], ['', "\n"], trim((string) $stmt));
        if ($time > 1000.0) {
            $level = \Aimeos\Base\Logger\Iface::NOTICE;
            $msg .= "\n" . (new \Exception())->get_trace_as_string();
        }
        $this->context()->logger()->log($msg, $level, 'core/sql');
        return $result;
    }
    /**
     * Returns the site coditions for the search request
     *
     * @param string[] $keys Sorted list of criteria keys
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
     * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
     * @return \Aimeos\Base\Criteria\Expression\Iface[] List of search conditions
     * @since 2015.01
     */
    protected function get_site_conditions(array $keys, array $attributes, int $sitelevel): array
    {
        $list = [];
        $entries = array_column($attributes, null, 'code');
        foreach ($keys as $key) {
            $name = $key . '.siteid';
            if (isset($entries[$name])) {
                $list[] = $this->site_condition($name, $sitelevel);
            } elseif (isset($entries['siteid'])) {
                $list[] = $this->site_condition('siteid', $sitelevel);
            }
        }
        return $list;
    }
    /**
     * Returns the SQL statement for the given config path
     *
     * If available, the database specific SQL statement is returned, otherwise
     * the ANSI SQL statement. The database type is determined via the resource
     * adapter.
     *
     * @param string $sql Configuration path to the SQL statement
     * @param array $replace Associative list of keys with strings to replace by their values
     * @return array|string ANSI or database specific SQL statement
     */
    protected function get_sql_config(string $sql, array $replace = []): string|array
    {
        if (preg_match('#^[a-z0-9\-]+(/[a-z0-9\-]+)*$#', $sql) === 1) {
            $config = $this->context()->config();
            $adapter = $config->get('resource/' . $this->get_resource_name() . '/adapter');
            if (($str = $config->get($sql . '/' . $adapter, $config->get($sql . '/ansi'))) === null) {
                $parts = explode('/', $sql);
                $cpath = 'mshop/common/manager/' . end($parts);
                $str = $config->get($cpath . '/' . $adapter, $config->get($cpath . '/ansi', $sql));
            }
            $sql = $str;
        }
        foreach ($replace as $key => $value) {
            $sql = str_replace($key, $value, $sql);
        }
        return str_replace([':alias', ':table'], [$this->alias(), $this->table()], $sql);
    }
    /**
     * Returns a search object singleton
     *
     * @return \Aimeos\Base\Criteria\Iface Search object
     */
    protected function get_search(): \Aimeos\Base\Criteria\Iface
    {
        if (!isset($this->search)) {
            $this->search = $this->filter();
        }
        return $this->search;
    }
    /**
     * Returns the string replacements for the SQL statements
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search critera object
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes Associative list of search keys and criteria attribute items as values for the base table
     * @param \Aimeos\Base\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
     * @param string[] $joins Associative list of SQL joins
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $columns Additional columns to retrieve values from
     * @return array Array of keys, find and replace arrays
     */
    protected function get_sql_replacements(\Aimeos\Base\Criteria\Iface $search, array $attributes, array $attronly, array $plugins, array $joins): array
    {
        $types = $this->get_search_types($attributes);
        $funcs = $this->get_search_functions($attributes);
        $trans = $this->get_search_translations($attributes);
        $trans = $this->alias_translations($trans);
        if (empty($search->get_sortations()) && ($attribute = reset($attronly)) !== false) {
            $search = (clone $search)->set_sortations([$search->sort('+', $attribute->get_code())]);
        }
        $sorts = $search->translate($search->get_sortations(), $trans, $funcs);
        $cols = $group = [];
        foreach ($attronly as $name => $entry) {
            if (str_contains($name, ':')) {
                continue;
            }
            if (empty($entry->get_internal_code())) {
                continue;
            }
            $icode = $entry->get_internal_code();
            if (!str_contains($icode, '"')) {
                $alias = $this->alias($entry->get_code());
                $icode = $alias . '."' . $icode . '"';
            }
            $cols[] = $icode . ' AS "' . $entry->get_code() . '"';
            $group[] = $icode;
        }
        return [':columns' => join(', ', $cols), ':joins' => join("\n", array_unique($joins)), ':group' => join(', ', array_unique(array_merge($group, $sorts))), ':cond' => $search->get_condition_source($types, $trans, $plugins, $funcs), ':order' => $search->get_sortation_source($types, $trans, $funcs), ':start' => $search->get_offset(), ':size' => $search->get_limit()];
    }
    /**
     * Returns the available sub-manager names
     *
     * @return array Sub-manager names, e.g. ['lists', 'property', 'type']
     */
    protected function get_sub_managers(): array
    {
        return $this->context()->config()->get($this->get_config_key('submanagers'), []);
    }
    /**
     * Returns the name of the used table
     *
     * @return string Table name e.g. "mshop_product_property_type"
     */
    protected function table(): string
    {
        return 'mshop_' . join('_', $this->type());
    }
    /**
     * Checks if the item is modified
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item object
     * @return bool True if the item is modified, false if not
     */
    protected function is_modified(\Aimeos\M_Shop\Common\Item\Iface $item): bool
    {
        return $item->is_modified();
    }
    /**
     * Returns the newly created ID for the last record which was inserted.
     *
     * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection used to insert the new record
     * @param string $cfgpath Configuration path to the SQL statement for retrieving the new ID of the last inserted record
     * @return string ID of the last record that was inserted by using the given connection
     * @throws \Aimeos\MShop\Exception if there's no ID of the last record available
     */
    protected function new_id(\Aimeos\Base\DB\Connection\Iface $conn, string $cfgpath): string
    {
        $sql = $this->get_sql_config($cfgpath);
        $result = $conn->create($sql)->execute();
        if (($row = $result->fetch(\Aimeos\Base\DB\Result\Base::FETCH_NUM)) === false) {
            $msg = $this->context()->translate('mshop', 'ID of last inserted database record not available');
            throw new \Aimeos\M_Shop\Exception($msg);
        }
        $result->finish();
        return $row[0];
    }
    /**
     * Saves an attribute item to the storage.
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item object
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
     */
    protected function save_base(\Aimeos\M_Shop\Common\Item\Iface $item, bool $fetch = true): \Aimeos\M_Shop\Common\Item\Iface
    {
        if (!$this->is_modified($item)) {
            return $this->object()->save_refs($item);
        }
        $context = $this->context();
        $conn = $context->db($this->get_resource_name());
        $id = $item->get_id();
        $columns = array_column($this->object()->get_save_attributes(), null, 'internalcode');
        if ($id === null) {
            /** mshop/common/manager/insert/mysql
             * Inserts a new record into the database table
             *
             * @see mshop/common/manager/insert/ansi
             */
            /** mshop/common/manager/insert/ansi
             * Inserts a new record into the database table
             *
             * Items with no ID yet (i.e. the ID is NULL) will be created in
             * the database and the newly created ID retrieved afterwards
             * using the "newid" SQL statement.
             *
             * The SQL statement must be a string suitable for being used as
             * prepared statement. It must include question marks for binding
             * the values from the item to the statement before they are
             * sent to the database server. The number of question marks must
             * be the same as the number of columns listed in the INSERT
             * statement. The order of the columns must correspond to the
             * order in the save() method, so the correct values are
             * bound to the columns.
             *
             * The SQL statement should conform to the ANSI standard to be
             * compatible with most relational database systems. This also
             * includes using double quotes for table and column names.
             *
             * @param string SQL statement for inserting records
             * @since 2023.10
             * @see mshop/common/manager/update/ansi
             * @see mshop/common/manager/newid/ansi
             * @see mshop/common/manager/delete/ansi
             * @see mshop/common/manager/search/ansi
             * @see mshop/common/manager/count/ansi
             */
            $path = $this->get_config_key('insert', 'mshop/common/manager/insert');
            $sql = $this->add_sql_columns(array_keys($columns), $this->get_sql_config($path));
        } else {
            /** mshop/common/manager/update/mysql
             * Updates an existing record in the database
             *
             * @see mshop/common/manager/update/ansi
             */
            /** mshop/common/manager/update/ansi
             * Updates an existing record in the database
             *
             * Items which already have an ID (i.e. the ID is not NULL) will
             * be updated in the database.
             *
             * The SQL statement must be a string suitable for being used as
             * prepared statement. It must include question marks for binding
             * the values from the item to the statement before they are
             * sent to the database server. The order of the columns must
             * correspond to the order in the save() method, so the
             * correct values are bound to the columns.
             *
             * The SQL statement should conform to the ANSI standard to be
             * compatible with most relational database systems. This also
             * includes using double quotes for table and column names.
             *
             * @param string SQL statement for updating records
             * @since 2023.10
             * @see mshop/common/manager/insert/ansi
             * @see mshop/common/manager/newid/ansi
             * @see mshop/common/manager/delete/ansi
             * @see mshop/common/manager/search/ansi
             * @see mshop/common/manager/count/ansi
             */
            $path = $this->get_config_key('update', 'mshop/common/manager/update');
            $sql = $this->add_sql_columns(array_keys($columns), $this->get_sql_config($path), false);
        }
        $idx = 1;
        $values = $item->to_array(true);
        $stmt = $this->get_cached_statement($conn, $path, $sql);
        foreach ($columns as $entry) {
            $value = $values[$entry->get_code()] ?? null;
            $value = $entry->get_type() === 'json' ? json_encode($value, JSON_FORCE_OBJECT) : $value;
            $stmt->bind($idx++, $value, \Aimeos\Base\Criteria\SQL::type($entry->get_type()));
        }
        $stmt = $this->bind($item, $stmt, $idx);
        $stmt->bind($idx++, $context->datetime());
        // mtime
        $stmt->bind($idx++, $context->editor());
        if ($id !== null) {
            $stmt->bind($idx++, $context->locale()->get_site_id() . '%');
            $stmt->bind($idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        } else {
            $stmt->bind($idx++, $this->site_id($item->get_site_id(), \Aimeos\M_Shop\Locale\Manager\Base::SITE_SUBTREE));
            $stmt->bind($idx++, $item->get_time_created() ?: $context->datetime());
            // ctime
        }
        $stmt->execute()->finish();
        if ($id === null) {
            /** mshop/common/manager/newid/mysql
             * Retrieves the ID generated by the database when inserting a new record
             *
             * @see mshop/common/manager/newid/ansi
             */
            /** mshop/common/manager/newid/ansi
             * Retrieves the ID generated by the database when inserting a new record
             *
             * As soon as a new record is inserted into the database table,
             * the database server generates a new and unique identifier for
             * that record. This ID can be used for retrieving, updating and
             * deleting that specific record from the table again.
             *
             * For MySQL:
             *  SELECT LAST_INSERT_ID()
             * For PostgreSQL:
             *  SELECT currval('seq_mcom_id')
             * For SQL Server:
             *  SELECT SCOPE_IDENTITY()
             * For Oracle:
             *  SELECT "seq_mcom_id".CURRVAL FROM DUAL
             *
             * There's no way to retrive the new ID by a SQL statements that
             * fits for most database servers as they implement their own
             * specific way.
             *
             * @param string SQL statement for retrieving the last inserted record ID
             * @since 2023.10
             * @see mshop/common/manager/insert/ansi
             * @see mshop/common/manager/update/ansi
             * @see mshop/common/manager/delete/ansi
             * @see mshop/common/manager/search/ansi
             * @see mshop/common/manager/count/ansi
             */
            $id = $this->new_id($conn, $this->get_config_key('newid', 'mshop/common/manager/newid'));
        }
        return $this->object()->save_refs($item->set_id($id));
    }
    /**
     * Returns the search result of the statement combined with the given criteria.
     *
     * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
     * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
     * @param string $cfgPathSearch Path to SQL statement in configuration for searching
     * @param string $cfgPathCount Path to SQL statement in configuration for counting
     * @param string[] $required Additional search keys to add conditions for even if no conditions are available
     * @param int|null $total Contains the number of all records matching the criteria if not null
     * @param int $sitelevel Constant from \Aimeos\MShop\Locale\Manager\Base for defining which site IDs should be used for searching
     * @param \Aimeos\Base\Criteria\Plugin\Iface[] $plugins Associative list of search keys and criteria plugin items as values
     * @return \Aimeos\Base\DB\Result\Iface SQL result object for accessing the found records
     * @throws \Aimeos\MShop\Exception if no number of all matching records is available
     */
    protected function search_items_base(\Aimeos\Base\DB\Connection\Iface $conn, \Aimeos\Base\Criteria\Iface $search, string $cfg_path_search, string $cfg_path_count, array $required, ?int &$total = null, int $sitelevel = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL, array $plugins = []): \Aimeos\Base\DB\Result\Iface
    {
        $attributes = $this->object()->get_search_attributes();
        $keys = $this->get_criteria_key_list($search, $required);
        $joins = $this->get_required_joins($attributes, $keys, array_shift($required));
        if (!empty($cond = $this->get_site_conditions($keys, $attributes, $sitelevel))) {
            $search = (clone $search)->add($search->and($cond));
        }
        $attronly = $this->object()->get_search_attributes(false);
        $replace = $this->get_sql_replacements($search, $attributes, $attronly, $plugins, $joins);
        if ($total !== null) {
            $sql = $this->get_sql_config($cfg_path_count, $replace);
            $result = $this->get_search_results($conn, $sql);
            $row = $result->fetch();
            $result->finish();
            if ($row === null) {
                $msg = $this->context()->translate('mshop', 'Total results value not found');
                throw new \Aimeos\M_Shop\Exception($msg);
            }
            $total = (int) $row['count'];
        }
        return $this->get_search_results($conn, $this->get_sql_config($cfg_path_search, $replace));
    }
    /**
     * Sets the name of the database resource that should be used.
     *
     * @param string $name Name of the resource
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
     */
    protected function set_resource_name(string $name): \Aimeos\M_Shop\Common\Manager\Iface
    {
        $config = $this->context()->config();
        if ($config->get('resource/' . $name) === null) {
            $this->resource_name = $config->get('resource/default', 'db');
        } else {
            $this->resource_name = $name;
        }
        return $this;
    }
    /**
     * Replaces the given marker with an expression
     *
     * @param string $column Name (including alias) of the column
     * @param mixed $value Value used in the expression
     * @param string $op Operator used in the expression
     * @param int $type Type constant from \Aimeos\Base\DB\Statement\Base class
     * @return string Created expression
     */
    protected function to_expression(string $column, $value, string $op = '==', int $type = \Aimeos\Base\DB\Statement\Base::PARAM_STR): string
    {
        $types = ['marker' => $type];
        $translations = ['marker' => $column];
        $value = is_array($value) ? array_unique($value) : $value;
        return $this->get_search()->compare($op, 'marker', $value)->to_source($types, $translations);
    }
    /**
     * Transforms the application specific values to Aimeos standard values.
     *
     * @param array $values Associative list of key/value pairs from the storage
     * @return array Associative list of key/value pairs with standard Aimeos values
     */
    protected function transform(array $values): array
    {
        return $values;
    }
    /**
     * Cuts the last part separated by a dot repeatedly and returns the list of resulting string.
     *
     * @param string[] $prefix Required base prefixes of the search keys
     * @param string $string String containing parts separated by dots
     * @return array List of resulting strings
     */
    private function cut_name_tail(array $prefix, string $string): array
    {
        $result = [];
        $noprefix = true;
        $strlen = strlen($string);
        foreach ($prefix as $key) {
            $len = strlen($key);
            if (strncmp($string, $key, $len) === 0) {
                if ($strlen > $len && ($pos = strrpos($string, '.')) !== false) {
                    $result[] = $string = substr($string, 0, $pos);
                    $result = array_merge($result, $this->cut_name_tail($prefix, $string));
                    $noprefix = false;
                }
                break;
            }
        }
        if ($noprefix) {
            if (($pos = strrpos($string, ':')) !== false) {
                $result[] = substr($string, 0, $pos);
                $result[] = $string;
            } elseif (($pos = strrpos($string, '.')) !== false) {
                $result[] = substr($string, 0, $pos);
            } else {
                $result[] = $string;
            }
        }
        return $result;
    }
    /**
     * Returns a list of unique criteria names shortend by the last element after the ''
     *
     * @param string[] $prefix Required base prefixes of the search keys
     * @param \Aimeos\Base\Criteria\Expression\Iface|null $expr Criteria object
     * @return array List of shortend criteria names
     */
    private function get_criteria_keys(array $prefix, ?\Aimeos\Base\Criteria\Expression\Iface $expr = null): array
    {
        if ($expr === null) {
            return [];
        }
        $result = [];
        foreach ($this->get_criteria_names($expr) as $item) {
            if (str_starts_with($item, 'sort:')) {
                $item = substr($item, 5);
            }
            if (($pos = strpos($item, '(')) !== false) {
                $item = substr($item, 0, $pos);
            }
            $result = array_merge($result, $this->cut_name_tail($prefix, $item));
        }
        return $result;
    }
    /**
     * Returns a list of criteria names from a expression and its sub-expressions.
     *
     * @param \Aimeos\Base\Criteria\Expression\Iface Criteria object
     * @return array List of criteria names
     */
    private function get_criteria_names(\Aimeos\Base\Criteria\Expression\Iface $expr): array
    {
        if ($expr instanceof \Aimeos\Base\Criteria\Expression\Compare\Iface) {
            return [$expr->get_name()];
        }
        if ($expr instanceof \Aimeos\Base\Criteria\Expression\Combine\Iface) {
            $list = [];
            foreach ($expr->get_expressions() as $item) {
                $list = array_merge($list, $this->get_criteria_names($item));
            }
            return $list;
        }
        if ($expr instanceof \Aimeos\Base\Criteria\Expression\Sort\Iface) {
            return [$expr->get_name()];
        }
        return [];
    }
}