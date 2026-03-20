<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Index
 */
namespace Aimeos\M_Shop\Index\Manager\Attribute;

/**
 * MySQL based index attribute for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class My_Sql extends \Aimeos\M_Shop\Index\Manager\Attribute\Standard
{
    private array $search_config = ['index.attribute.id' => ['code' => 'index.attribute.id', 'internalcode' => 'mindat."attrid"', 'internaldeps' => ['LEFT JOIN "mshop_index_attribute" AS mindat USE INDEX ("idx_msindat_s_at_lt", "unq_msindat_p_s_aid_lt") ON mindat."prodid" = mpro."id"'], 'label' => 'Product index attribute ID']];
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