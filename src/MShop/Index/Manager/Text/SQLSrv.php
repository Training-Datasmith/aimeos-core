<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package MShop
 * @subpackage Index
 */
namespace Aimeos\M_Shop\Index\Manager\Text;

/**
 * SQL Server based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class Sql_Srv extends \Aimeos\M_Shop\Index\Manager\Text\Standard
{
    private array $search_config = ['index.text:relevance' => ['code' => 'index.text:relevance()', 'label' => 'Product texts, parameter(<language ID>,<search term>)', 'type' => 'float', 'public' => false], 'sort:index.text:relevance' => ['code' => 'sort:index.text:relevance()', 'label' => 'Product text sorting, parameter(<language ID>,<search term>)', 'type' => 'float', 'public' => false]];
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context)
    {
        parent::__construct($context);
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL;
        $level = $context->config()->get('mshop/index/manager/sitemode', $level);
        if ($context->config()->get('mshop/index/manager/text/sqlsrv/fulltext', false)) {
            $search = ':site AND mindte."langid" = $1 AND (
				SELECT mindte_ft.RANK
				FROM CONTAINSTABLE("mshop_index_text", "content", $2) AS mindte_ft
				WHERE mindte."id" = mindte_ft."KEY"
			)';
            $sort = 'mindte_ft.RANK * mpro."boost"';
            $func = $this->get_function_relevance();
        } else {
            $search = ':site AND mindte."langid" = $1 AND CHARINDEX( $2, content )';
            $sort = '-CHARINDEX( $2, content ) * mpro."boost"';
            $func = function ($source, array $params): array {
                if (isset($params[1])) {
                    $params[1] = mb_strtolower($params[1]);
                }
                return $params;
            };
        }
        $expr = $this->site_string('mindte."siteid"', $level);
        $this->search_config['index.text:relevance']['internalcode'] = str_replace(':site', $expr, $search);
        $this->search_config['sort:index.text:relevance']['internalcode'] = $sort;
        $this->search_config['index.text:relevance']['function'] = $func;
    }
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
    /**
     * Returns the search function for searching by relevance
     *
     * @return \Closure Relevance search function
     */
    protected function get_function_relevance()
    {
        return function ($source, array $params): array {
            if (isset($params[1])) {
                $strings = [];
                $regex = '/(\&|\||\!|\-|\+|\>|\<|\(|\)|\~|\*|\:|\"|\'|\@|\| )+/';
                $search = trim(mb_strtolower(preg_replace($regex, ' ', $params[1])), "' \t\n\r\x00\v");
                foreach (explode(' ', $search) as $part) {
                    if (strlen($part) > 2) {
                        $strings[] = '"' . $part . '*"';
                    }
                }
                $params[1] = '\'' . join(' | ', $strings) . '\'';
            }
            return $params;
        };
    }
}