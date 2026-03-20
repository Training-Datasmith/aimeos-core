<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MW
 * @subpackage Tree
 */
namespace Aimeos\MW\Tree\Manager;

/**
 * Tree manager using nested sets stored in a database.
 *
 * @package MW
 * @subpackage Tree
 */
class Db_Nested_Set extends \Aimeos\MW\Tree\Manager\Base
{
    private array $search_config = [];
    private array $config;
    private \Aimeos\Base\DB\Connection\Iface $conn;
    /**
     * Initializes the tree manager.
     *
     * The config['search] array must contain these key/array pairs suitable for \Aimeos\Base\Criteria\Attribute\Standard:
     *	[id] => Array describing unique ID codes/types/labels
     *	[label] => Array describing codes/types/labels for descriptive labels
     *	[status] => Array describing codes/types/labels for status values
     *	[parentid] => Array describing codes/types/labels for parentid values
     *	[level] => Array describing codes/types/labels for height levels of tree nodes
     *	[left] => Array describing codes/types/labels for nodes left values
     *	[right] => Array describing codes/types/labels for nodes right values
     *
     * The config['sql] array must contain these statement:
     *	[delete] =>
     *		DELETE FROM treetable WHERE left >= ? AND right <= ?
     *	[get] =>
     *		SELECT node.*
     *		FROM treetable AS parent, treetable AS node
     *		WHERE node.left >= parent.left AND node.left <= parent.right
     *		AND parent.id = ? AND node.level <= parent.level + ? AND :cond
     *		ORDER BY node.left
     *	[insert] =>
     *		INSERT INTO treetable ( label, code, status, parentid, level, left, right ) VALUES ( ?, ?, ?, ?, ? )
     *	[move-left] =>
     *		UPDATE treetable
     *		SET left = left + ?, level = level + ?
     *		WHERE left >= ? AND left <= ?
     *	[move-right] =>
     *		UPDATE treetable
     *		SET right = right + ?
     *		WHERE right >= ? AND right <= ?
     *	[search] =>
     *		SELECT * FROM treetable
     *		WHERE left >= ? AND right <= ? AND :cond
     *		ORDER BY :order
     *	[update] =>
     *		UPDATE treetable SET label = ?, code = ?, status = ? WHERE id = ?
     *	[update-parentid] =>
     *		UPDATE treetable SET parentid = ? WHERE id = ?
     *	[newid] =>
     *		SELECT LAST_INSERT_ID()
     *
     * @param array $config Associative array holding the SQL statements
     * @param \Aimeos\Base\DB\Connection\Iface $resource Database connection
     */
    public function __construct(array $config, \Aimeos\Base\DB\Connection\Iface $resource)
    {
        if (!isset($config['search'])) {
            throw new \Aimeos\MW\Tree\Exception('Search config is missing');
        }
        if (!isset($config['sql'])) {
            throw new \Aimeos\MW\Tree\Exception('SQL config is missing');
        }
        $this->check_search_config($config['search']);
        $this->check_sql_config($config['sql']);
        $this->search_config = $config['search'];
        $this->config = $config['sql'];
        $this->conn = $resource;
    }
    /**
     * Returns a list of attributes which can be used in the search method.
     *
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
     */
    public function get_search_attributes(): array
    {
        $attributes = [];
        foreach ($this->search_config as $values) {
            $attributes[] = new \Aimeos\Base\Criteria\Attribute\Standard($values);
        }
        return $attributes;
    }
    /**
     * Creates a new search object for storing search criterias.
     *
     * @return \Aimeos\Base\Criteria\Iface Search object instance
     */
    public function create_search(): \Aimeos\Base\Criteria\Iface
    {
        return new \Aimeos\Base\Criteria\SQL($this->conn);
    }
    /**
     * Creates a new node object.
     *
     * @return \Aimeos\MW\Tree\Node\Iface Empty node object
     */
    public function create_node(): \Aimeos\MW\Tree\Node\Iface
    {
        return $this->create_node_base();
    }
    /**
     * Deletes a node and its descendants from the storage.
     *
     * @param string|null $id Delete the node with the ID and all nodes below
     * @return \Aimeos\MW\Tree\Manager\Iface Manager object for method chaining
     */
    public function delete_node(?string $id = null): Iface
    {
        $node = $this->get_node($id, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE);
        $stmt = $this->conn->create($this->config['delete']);
        $stmt->bind(1, $node->left, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, $node->right, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        $diff = $node->right - $node->left + 1;
        $stmt = $this->conn->create($this->config['move-left']);
        $stmt->bind(1, -$diff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, 0, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(3, $node->right + 1, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(4, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        $stmt = $this->conn->create($this->config['move-right']);
        $stmt->bind(1, -$diff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, $node->right + 1, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(3, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        return $this;
    }
    /**
     * Returns a node and its descendants depending on the given resource.
     *
     * @param string|null $id Retrieve nodes starting from the given ID
     * @param int $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
     * @param \Aimeos\Base\Criteria\Iface|null $condition Optional criteria object with conditions
     * @return \Aimeos\MW\Tree\Node\Iface Node, maybe with subnodes
     */
    public function get_node(?string $id = null, int $level = Base::LEVEL_TREE, ?\Aimeos\Base\Criteria\Iface $condition = null): \Aimeos\MW\Tree\Node\Iface
    {
        if ($id === null) {
            if (($node = $this->get_root_node()) === null) {
                throw new \Aimeos\MW\Tree\Exception('No root node available');
            }
            if ($level === \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE) {
                return $node;
            }
        } else {
            $node = $this->get_node_by_id($id);
            if ($level === \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE) {
                return $node;
            }
        }
        $id = $node->get_id();
        $numlevel = $this->get_level_from_constant($level);
        $search = $condition ?: $this->create_search();
        $types = $this->get_search_types($this->search_config);
        $funcs = $this->get_search_functions($this->search_config);
        $translations = $this->get_search_translations($this->search_config);
        $conditions = $search->get_condition_source($types, $translations, [], $funcs);
        $stmt = $this->conn->create(str_replace(':cond', $conditions, $this->config['get']));
        $stmt->bind(1, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, $numlevel, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $result = $stmt->execute();
        if (($row = $result->fetch()) === null) {
            throw new \Aimeos\MW\Tree\Exception(sprintf('No node with ID "%1$d" found', $id));
        }
        $node = $this->create_node_base($row);
        $this->create_tree($result, $node);
        return $node;
    }
    /**
     * Inserts a new node before the given reference node to the parent in the storage.
     *
     * @param \Aimeos\MW\Tree\Node\Iface $node New node that should be inserted
     * @param string|null $parentId ID of the parent node where the new node should be inserted below (null for root node)
     * @param string|null $refId ID of the node where the node should be inserted before (null to append)
     * @return \Aimeos\MW\Tree\Node\Iface Updated node item
     */
    public function insert_node(\Aimeos\MW\Tree\Node\Iface $node, ?string $parent_id = null, ?string $ref_id = null): \Aimeos\MW\Tree\Node\Iface
    {
        $node->parentid = $parent_id;
        if ($ref_id !== null) {
            $ref_node = $this->get_node($ref_id, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE);
            $node->left = $ref_node->left;
            $node->right = $ref_node->left + 1;
            $node->level = $ref_node->level;
        } elseif ($parent_id !== null) {
            $parent_node = $this->get_node($parent_id, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE);
            $node->left = $parent_node->right;
            $node->right = $parent_node->right + 1;
            $node->level = $parent_node->level + 1;
        } else {
            $node->left = 1;
            $node->right = 2;
            $node->level = 0;
            $node->parentid = 0;
            if (($root = $this->get_root_node('-')) !== null) {
                $node->left = $root->right + 1;
                $node->right = $root->right + 2;
            }
        }
        $stmt = $this->conn->create($this->config['move-left']);
        $stmt->bind(1, 2, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, 0, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(3, $node->left, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(4, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        $stmt = $this->conn->create($this->config['move-right']);
        $stmt->bind(1, 2, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, $node->left, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(3, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        $stmt = $this->conn->create($this->config['insert']);
        $stmt->bind(1, $node->get_label(), \Aimeos\Base\DB\Statement\Base::PARAM_STR);
        $stmt->bind(2, $node->get_code(), \Aimeos\Base\DB\Statement\Base::PARAM_STR);
        $stmt->bind(3, $node->get_status(), \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(4, (int) $node->parentid, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(5, $node->level, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(6, $node->left, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(7, $node->right, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        $result = $this->conn->create($this->config['newid'])->execute();
        if (($row = $result->fetch(\Aimeos\Base\DB\Result\Base::FETCH_NUM)) === false) {
            throw new \Aimeos\MW\Tree\Exception(sprintf('No new record ID available'));
        }
        $result->finish();
        $node->set_id($row[0]);
        return $node;
    }
    /**
     * Moves an existing node to the new parent in the storage.
     *
     * @param string $id ID of the node that should be moved
     * @param string|null $oldParentId ID of the old parent node which currently contains the node that should be removed
     * @param string|null $newParentId ID of the new parent node where the node should be moved to
     * @param string|null $newRefId ID of the node where the node should be inserted before (null to append)
     * @return \Aimeos\MW\Tree\Manager\Iface Manager object for method chaining
     */
    public function move_node(string $id, ?string $old_parent_id = null, ?string $new_parent_id = null, ?string $new_ref_id = null): Iface
    {
        $node = $this->get_node($id, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE);
        $diff = $node->right - $node->left + 1;
        if ($new_ref_id !== null) {
            $ref_node = $this->get_node($new_ref_id, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE);
            $leveldiff = $ref_node->level - $node->level;
            $open_node_left_begin = $ref_node->left;
            $open_node_right_begin = $ref_node->left + 1;
            if ($ref_node->left < $node->left) {
                $move_node_left_begin = $node->left + $diff;
                $move_node_left_end = $node->right + $diff - 1;
                $move_node_right_begin = $node->left + $diff + 1;
                $move_node_right_end = $node->right + $diff;
                $movesize = $ref_node->left - $node->left - $diff;
            } else {
                $move_node_left_begin = $node->left;
                $move_node_left_end = $node->right - 1;
                $move_node_right_begin = $node->left + 1;
                $move_node_right_end = $node->right;
                $movesize = $ref_node->left - $node->left;
            }
            $close_node_left_begin = $node->left + $diff;
            $close_node_right_begin = $node->left + $diff;
        } else {
            $ref_node = $this->get_node($new_parent_id, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE);
            if ($new_parent_id === null) {
                //make virtual root
                if (($root = $this->get_root_node('-')) !== null) {
                    $ref_node->left = $root->right;
                    $ref_node->right = $root->right + 1;
                    $ref_node->level = -1;
                }
            }
            $leveldiff = $ref_node->level - $node->level + 1;
            $open_node_left_begin = $ref_node->right + 1;
            $open_node_right_begin = $ref_node->right;
            if ($ref_node->right < $node->right) {
                $move_node_left_begin = $node->left + $diff;
                $move_node_left_end = $node->right + $diff - 1;
                $move_node_right_begin = $node->left + $diff + 1;
                $move_node_right_end = $node->right + $diff;
                $movesize = $ref_node->right - $node->left - $diff;
            } else {
                $move_node_left_begin = $node->left;
                $move_node_left_end = $node->right - 1;
                $move_node_right_begin = $node->left + 1;
                $move_node_right_end = $node->right;
                $movesize = $ref_node->right - $node->left;
            }
            $close_node_left_begin = $node->left + $diff;
            $close_node_right_begin = $node->left + $diff;
        }
        $stmt_left = $this->conn->create($this->config['move-left']);
        $stmt_right = $this->conn->create($this->config['move-right']);
        $update_parent_id = $this->conn->create($this->config['update-parentid']);
        // open gap for inserting node or subtree
        $stmt_left->bind(1, $diff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(2, 0, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(3, $open_node_left_begin, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(4, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->execute()->finish();
        $stmt_right->bind(1, $diff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->bind(2, $open_node_right_begin, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->bind(3, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->execute()->finish();
        // move node or subtree to the new gap
        $stmt_left->bind(1, $movesize, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(2, $leveldiff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(3, $move_node_left_begin, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(4, $move_node_left_end, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->execute()->finish();
        $stmt_right->bind(1, $movesize, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->bind(2, $move_node_right_begin, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->bind(3, $move_node_right_end, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->execute()->finish();
        // close gap opened by moving the node or subtree to the new location
        $stmt_left->bind(1, -$diff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(2, 0, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(3, $close_node_left_begin, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->bind(4, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_left->execute()->finish();
        $stmt_right->bind(1, -$diff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->bind(2, $close_node_right_begin, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->bind(3, 0x7fffffff, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt_right->execute()->finish();
        $update_parent_id->bind(1, (int) $new_parent_id, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $update_parent_id->bind(2, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $update_parent_id->execute()->finish();
        return $this;
    }
    /**
     * Stores the values of the given node to the storage.
     *
     * This method does only store values like the node label but doesn't change
     * the tree layout by adding, moving or deleting nodes.
     *
     * @param \Aimeos\MW\Tree\Node\Iface $node Tree node object
     * @return \Aimeos\MW\Tree\Node\Iface Updated node item
     */
    public function save_node(\Aimeos\MW\Tree\Node\Iface $node): \Aimeos\MW\Tree\Node\Iface
    {
        if ($node->get_id() === null) {
            throw new \Aimeos\MW\Tree\Exception(sprintf('Unable to save newly created nodes, use insert method instead'));
        }
        if ($node->is_modified() === false) {
            return $node;
        }
        $stmt = $this->conn->create($this->config['update']);
        $stmt->bind(1, $node->get_label(), \Aimeos\Base\DB\Statement\Base::PARAM_STR);
        $stmt->bind(2, $node->get_code(), \Aimeos\Base\DB\Statement\Base::PARAM_STR);
        $stmt->bind(3, $node->get_status(), \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(4, $node->get_id(), \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->execute()->finish();
        return $node;
    }
    /**
     * Retrieves a list of nodes from the storage matching the given search criteria.
     *
     * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
     * @param string|null $id Search nodes starting at the node with the given ID
     * @return \Aimeos\MW\Tree\Node\Iface[] List of tree nodes
     */
    public function search_nodes(\Aimeos\Base\Criteria\Iface $search, ?string $id = null): array
    {
        $left = 1;
        $right = 0x7fffffff;
        if ($id !== null) {
            $node = $this->get_node_by_id($id);
            $left = $node->left;
            $right = $node->right;
        }
        if ($search->get_sortations() === []) {
            $search->set_sortations([$search->sort('+', $this->search_config['left']['code'])]);
        }
        $types = $this->get_search_types($this->search_config);
        $funcs = $this->get_search_functions($this->search_config);
        $translations = $this->get_search_translations($this->search_config);
        $conditions = $search->get_condition_source($types, $translations, [], $funcs);
        $sortations = $search->get_sortation_source($types, $translations, $funcs);
        $sql = str_replace([':cond', ':order', ':size', ':start'], [$conditions, $sortations, $search->get_limit(), $search->get_offset()], $this->config['search']);
        $stmt = $this->conn->create($sql);
        $stmt->bind(1, $left, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, $right, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $result = $stmt->execute();
        try {
            $nodes = [];
            while ($row = $result->fetch()) {
                $nodes[$row['id']] = $this->create_node_base($row);
            }
        } catch (\Exception $e) {
            $result->finish();
            throw $e;
        }
        return $nodes;
    }
    /**
     * Returns a list if node IDs, that are in the path of given node ID.
     *
     * @param string $id ID of node to get the path for
     * @return \Aimeos\MW\Tree\Node\Iface[] List of tree nodes
     */
    public function get_path(string $id): array
    {
        $result = [];
        $node = $this->get_node($id, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE);
        $search = $this->create_search();
        $expr = [$search->compare('<=', $this->search_config['left']['code'], $node->left), $search->compare('>=', $this->search_config['right']['code'], $node->right)];
        $search->set_conditions($search->and($expr));
        $search->set_sortations([$search->sort('+', $this->search_config['left']['code'])]);
        foreach ($this->search_nodes($search) as $item) {
            $result[$item->get_id()] = $item;
        }
        return $result;
    }
    /**
     * Checks if all required search configurations are available.
     *
     * @param array $config Associative list of search configurations
     * @throws \Aimeos\MW\Tree\Exception If one ore more search configurations are missing
     */
    protected function check_search_config(array $config)
    {
        $required = ['id', 'label', 'status', 'level', 'left', 'right'];
        foreach ($required as $key => $entry) {
            if (isset($config[$entry])) {
                unset($required[$key]);
            }
        }
        if (count($required) > 0) {
            $msg = 'Search config in given configuration are missing: "%1$s"';
            throw new \Aimeos\MW\Tree\Exception(sprintf($msg, implode(', ', $required)));
        }
    }
    /**
     * Checks if all required SQL statements are available.
     *
     * @param array $config Associative list of SQL statements
     * @throws \Aimeos\MW\Tree\Exception If one ore more SQL statements are missing
     */
    protected function check_sql_config(array $config)
    {
        $required = ['delete', 'get', 'insert', 'move-left', 'move-right', 'search', 'update', 'newid'];
        foreach ($required as $key => $entry) {
            if (isset($config[$entry])) {
                unset($required[$key]);
            }
        }
        if (count($required) > 0) {
            $msg = 'SQL statements in given configuration are missing: "%1$s"';
            throw new \Aimeos\MW\Tree\Exception(sprintf($msg, implode(', ', $required)));
        }
    }
    /**
     * Creates a new node object.
     *
     * @param array $values List of attributes that should be stored in the new node
     * @param \Aimeos\MW\Tree\Node\Iface[] $children List of child nodes
     * @return \Aimeos\MW\Tree\Node\Iface Empty node object
     */
    protected function create_node_base(array $values = [], array $children = []): \Aimeos\MW\Tree\Node\Iface
    {
        return new \Aimeos\MW\Tree\Node\Db_Nested_Set($values, $children);
    }
    /**
     * Creates a tree from the result set returned by the database.
     *
     * @param \Aimeos\Base\DB\Result\Iface $result Database result
     * @param \Aimeos\MW\Tree\Node\Iface $node Current node to add children to
     */
    protected function create_tree(\Aimeos\Base\DB\Result\Iface $result, \Aimeos\MW\Tree\Node\Iface $node): ?\Aimeos\MW\Tree\Node\Iface
    {
        while ($record = $result->fetch()) {
            $new_node = $this->create_node_base($record);
            while ($this->is_child($new_node, $node)) {
                if ($new_node->__get('level') > $node->__get('level') + 1) {
                    continue 2;
                }
                $node->add_child($new_node);
                if (($new_node = $this->create_tree($result, $new_node)) === null) {
                    return null;
                }
            }
            return $new_node;
        }
        return null;
    }
    /**
     * Tests if the first node is a child of the second node.
     *
     * @param \Aimeos\MW\Tree\Node\Iface $node Node to test
     * @param \Aimeos\MW\Tree\Node\Iface $parent Parent node
     * @return bool True if not is a child of the second node, false if not
     */
    protected function is_child(\Aimeos\MW\Tree\Node\Iface $node, \Aimeos\MW\Tree\Node\Iface $parent): bool
    {
        return $node->__get('left') > $parent->__get('left') && $node->__get('right') < $parent->__get('right');
    }
    /**
     * Converts the level constant to the depth of the tree.
     *
     * @param int $level Level constant from \Aimeos\MW\Tree\Manager\Base
     * @return int Number of tree levels
     * @throws \Aimeos\MW\Tree\Exception if level constant is invalid
     */
    protected function get_level_from_constant(int $level): int
    {
        return match ($level) {
            \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE => 0,
            \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST => 1,
            \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE => 0x3fff,
            default => throw new \Aimeos\MW\Tree\Exception(sprintf('Invalid level constant "%1$d"', $level)),
        };
    }
    /**
     * Returns a single node identified by its ID.
     *
     * @param string $id Unique ID
     * @return \Aimeos\MW\Tree\Node\Iface Tree node
     * @throws \Aimeos\MW\Tree\Exception If node is not found
     * @throws \Exception If anything unexcepted occurs
     */
    protected function get_node_by_id(string $id): \Aimeos\MW\Tree\Node\Iface
    {
        $stmt = $this->conn->create(str_replace(':cond', '1=1', $this->config['get']));
        $stmt->bind(1, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $stmt->bind(2, 0, \Aimeos\Base\DB\Statement\Base::PARAM_INT);
        $result = $stmt->execute();
        if (($row = $result->fetch()) === null) {
            throw new \Aimeos\MW\Tree\Exception(sprintf('No node with ID "%1$d" found', $id));
        }
        return $this->create_node_base($row);
    }
    /**
     * Returns the first tree root node depending on the sorting direction.
     *
     * @param string $sort Sort direction, '+' is ascending, '-' is descending
     * @return \Aimeos\MW\Tree\Node\Iface|null Tree root node
     */
    protected function get_root_node(string $sort = '+'): ?\Aimeos\MW\Tree\Node\Iface
    {
        $search = $this->create_search();
        $search->set_conditions($search->compare('==', $this->search_config['level']['code'], 0));
        $search->set_sortations([$search->sort($sort, $this->search_config['left']['code'])]);
        $nodes = $this->search_nodes($search);
        if (($node = reset($nodes)) !== false) {
            return $node;
        }
        return null;
    }
}