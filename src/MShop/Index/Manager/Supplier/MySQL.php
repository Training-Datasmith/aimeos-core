<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Index
 */
namespace Aimeos\M_Shop\Index\Manager\Supplier;

/**
 * MySQL based index supplier for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class My_Sql extends \Aimeos\M_Shop\Index\Manager\Supplier\Standard
{
    private array $search_config = ['index.supplier.id' => ['code' => 'index.supplier.id', 'internalcode' => 'mindsu."supid"', 'internaldeps' => ['LEFT JOIN "mshop_index_supplier" AS mindsu USE INDEX ("idx_msindsup_sid_supid_lt_po", "unq_msindsu_p_s_lt_si_po_la_lo") ON mindsu."prodid" = mpro."id"'], 'label' => 'Product index supplier ID']];
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