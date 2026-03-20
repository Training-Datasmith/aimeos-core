<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Index
 */
namespace Aimeos\M_Shop\Index\Manager\Catalog;

/**
 * MySQL based index catalog for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class My_Sql extends \Aimeos\M_Shop\Index\Manager\Catalog\Standard
{
    private array $search_config = ['index.catalog.id' => ['code' => 'index.catalog.id', 'internalcode' => 'mindca."catid"', 'internaldeps' => ['LEFT JOIN "mshop_index_catalog" AS mindca USE INDEX ("idx_msindca_s_ca_lt_po", "unq_msindca_p_s_cid_lt_po") ON mindca."prodid" = mpro."id"'], 'label' => 'Product index category ID']];
    /**
     * Returns a list of objects describing the available criterias for searching.
     *
     * @param bool $withsub Return also attributes of sub-managers if true
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attriubte items
     */
    public function get_search_attributes(bool $withsub = true): array
    {
        $list = parent::get_search_attributes($withsub);
        foreach ($this->search_config as $key => $fields) {
            $list[$key] = new \Aimeos\Base\Criteria\Attribute\Standard($fields);
        }
        return $list;
    }
}