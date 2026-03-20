<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager\Decorator;

/**
 * Provides a decorator for fetching type items
 *
 * @package MShop
 * @subpackage Common
 */
class Type extends \Aimeos\M_Shop\Common\Manager\Decorator\Base
{
    /**
     * Merges the data from the given map and the referenced items
     *
     * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
     * @param array $ref List of referenced items to fetch and add to the entries
     * @return array Associative list of ID as key and the updated entries as value
     */
    public function search_refs(array $entries, array $ref): array
    {
        $entries = $this->get_manager()->search_refs($entries, $ref);
        $type = $this->get_manager()->type();
        $path = join('/', $type);
        if ($this->has_ref($ref, $path . '/type') && !empty($entries)) {
            $key = join('.', $type) . '.type';
            $code = $key . '.code';
            if (!empty($values = array_column($entries, $key))) {
                $manager = \Aimeos\M_Shop::create($this->context(), $path . '/type');
                $filter = $manager->filter(true)->slice(0, 0x7fffffff)->add([$code => $values]);
                $type_items = $manager->search($filter)->group_by($code);
                foreach ($entries as $id => $entry) {
                    foreach ($type_items[$entry[$key]] ?? [] as $type_item) {
                        $entries[$id]['.type'] = $type_item;
                    }
                }
            }
        }
        return $entries;
    }
}