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
 * Abstract tree manager class with basic methods.
 *
 * @package MW
 * @subpackage Tree
 */
abstract class Base implements \Aimeos\MW\Tree\Manager\Iface
{
    /**
     * Returns only the requested node
     */
    public const LEVEL_ONE = 1;
    /**
     * Returns the requested node and its children
     */
    public const LEVEL_LIST = 2;
    /**
     * Returns all subnodes including the requested one
     */
    public const LEVEL_TREE = 3;
    private bool $read_only = false;
    /**
     * Returns the attribute helper functions for searching defined by the manager.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
     * @return array Associative array of attribute code and helper function
     */
    protected function get_search_functions(array $attributes): array
    {
        $list = [];
        $iface = \Aimeos\Base\Criteria\Attribute\Iface::class;
        foreach ($attributes as $key => $item) {
            if ($item instanceof $iface) {
                $list[$item->get_code()] = $item->get_function();
            } elseif (isset($item['code'])) {
                $list[$item['code']] = $item['function'] ?? null;
            } else {
                throw new \Aimeos\MW\Tree\Exception(sprintf('Invalid attribute at position "%1$d"', $key));
            }
        }
        return $list;
    }
    /**
     * Returns the attribute translations for searching defined by the manager.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
     * @return array Associative array of attribute code and internal attribute code
     */
    protected function get_search_translations(array $attributes): array
    {
        $translations = [];
        $iface = \Aimeos\Base\Criteria\Attribute\Iface::class;
        foreach ($attributes as $key => $item) {
            if ($item instanceof $iface) {
                $translations[$item->get_code()] = $item->get_internal_code();
            } elseif (isset($item['code'])) {
                $translations[$item['code']] = $item['internalcode'];
            } else {
                throw new \Aimeos\MW\Tree\Exception(sprintf('Invalid attribute at position "%1$d"', $key));
            }
        }
        return $translations;
    }
    /**
     * Returns the attribute types for searching defined by the manager.
     *
     * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
     * @return array Associative array of attribute code and internal attribute type
     */
    protected function get_search_types(array $attributes): array
    {
        $types = [];
        $iface = \Aimeos\Base\Criteria\Attribute\Iface::class;
        foreach ($attributes as $key => $item) {
            if ($item instanceof $iface) {
                $types[$item->get_code()] = $item->get_internal_type();
            } elseif (isset($item['code'])) {
                $types[$item['code']] = $item['type'];
            } else {
                throw new \Aimeos\MW\Tree\Exception(sprintf('Invalid attribute at position "%1$d"', $key));
            }
        }
        return $types;
    }
    /**
     * Checks, whether a tree is read only.
     *
     * @return bool True if tree is read-only, false if not
     */
    public function is_read_only(): bool
    {
        return $this->read_only;
    }
    /**
     * Sets this manager to read only.
     *
     * @param bool $flag True if tree is read-only, false if not
     */
    protected function set_read_only(bool $flag = true): Iface
    {
        $this->read_only = $flag;
        return $this;
    }
}