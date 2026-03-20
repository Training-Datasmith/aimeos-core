<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package MShop
 * @subpackage Index
 */
namespace Aimeos\M_Shop\Index\Manager;

/**
 * MySQL index index manager for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class Pg_Sql extends \Aimeos\M_Shop\Index\Manager\Standard implements \Aimeos\M_Shop\Index\Manager\Iface, \Aimeos\M_Shop\Common\Manager\Factory\Iface
{
    private ?array $sub_managers = null;
    /**
     * Returns a new manager for product extensions.
     *
     * @param string $manager Name of the sub manager type in lower case
     * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
     * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
     */
    public function get_sub_manager(string $manager, ?string $name = null): \Aimeos\M_Shop\Common\Manager\Iface
    {
        return $this->get_sub_manager_base('index', $manager, $name ?: 'PgSQL');
    }
    /**
     * Returns the list of sub-managers available for the index attribute manager.
     *
     * @return \Aimeos\MShop\Index\Manager\Iface[] Associative list of the sub-domain as key and the manager object as value
     */
    protected function get_sub_managers(): array
    {
        if ($this->sub_managers === null) {
            $this->sub_managers = [];
            $config = $this->context()->config();
            foreach ($config->get('mshop/index/manager/submanagers', []) as $domain) {
                $name = $config->get('mshop/index/manager/' . $domain . '/name');
                $this->sub_managers[$domain] = $this->object()->get_sub_manager($domain, $name ?: 'PgSQL');
            }
            return $this->sub_managers;
        }
        return $this->sub_managers;
    }
}