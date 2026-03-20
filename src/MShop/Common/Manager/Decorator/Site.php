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
 * Provides a decorator for fetching site items
 *
 * @package MShop
 * @subpackage Common
 */
class Site extends \Aimeos\M_Shop\Common\Manager\Decorator\Base
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
        if ($this->has_ref($ref, 'locale/site') && !empty($entries)) {
            $manager = \Aimeos\M_Shop::create($this->context(), 'locale/site');
            $key = join('.', $this->get_manager()->type()) . '.siteid';
            $site_ids = array_column($entries, $key);
            $filter = $manager->filter(true)->add(['locale.site.siteid' => $site_ids])->slice(0, 0x7fffffff);
            $site_items = $manager->search($filter)->col(null, 'locale.site.siteid');
            foreach ($entries as $id => $entry) {
                $entries[$id]['.locale/site'] = $site_items[$entry[$key]] ?? null;
            }
        }
        return $entries;
    }
}