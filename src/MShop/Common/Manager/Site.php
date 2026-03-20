<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager;

use Aimeos\M_Shop\Locale\Manager\Base as Locale;
/**
 * Site trait for managers
 *
 * @package MShop
 * @subpackage Common
 */
trait Site
{
    private static array $site_inactive = [];
    private static int $last_refresh = 0;
    /**
     * Returns the context object.
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    abstract protected function context(): \Aimeos\M_Shop\Context_Iface;
    /**
     * Returns a filter object.
     *
     * @return \Aimeos\Base\Criteria\Iface Filter object
     */
    abstract public function filter(?bool $default = false, bool $site = false): \Aimeos\Base\Criteria\Iface;
    /**
     * Returns the type of the mananger as separate parts
     *
     * @return string[] List of manager part names
     */
    abstract public function type(): array;
    /**
     * Returns the site expression for the given name
     *
     * @param string $name Name of the site condition
     * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
     * @return \Aimeos\Base\Criteria\Expression\Iface Site search condition
     * @since 2022.04
     */
    protected function site_condition(string $name, int $sitelevel): \Aimeos\Base\Criteria\Expression\Iface
    {
        $sites = $this->context()->locale()->get_sites();
        $current = $sites[Locale::SITE_ONE] ?? null;
        $values = [''];
        if (isset($sites[Locale::SITE_PATH]) && $sitelevel & Locale::SITE_PATH) {
            $values = array_merge($values, $sites[Locale::SITE_PATH]);
        } elseif ($current) {
            $values[] = $current;
        }
        $filter = $this->filter();
        $cond = $filter->compare('==', $name, $values);
        if (isset($sites[Locale::SITE_SUBTREE]) && $sitelevel & Locale::SITE_SUBTREE) {
            $cond = $filter->or([$cond, $filter->compare('=~', $name, $sites[Locale::SITE_SUBTREE])]);
        }
        if ($current && !($inactive = $this->site_inactive($current))->is_empty()) {
            return $filter->and([$cond, $filter->is($name, '!=', $inactive)]);
        }
        return $cond;
    }
    /**
     * Returns the site IDs that are inactive
     *
     * @param string $current Current site ID
     * @return \Aimeos\Map List of inactive site IDs
     */
    protected function site_inactive(string $current): \Aimeos\Map
    {
        // Required for fetching customer item below
        if (in_array(join('/', $this->type()), ['customer', 'customer/lists', 'group'])) {
            return map();
        }
        if (self::$last_refresh < ($time = time()) - 60) {
            // clear cache regularly for Laravel Octane
            self::$last_refresh = $time;
            self::$site_inactive = [];
        }
        if (!isset(self::$site_inactive[$current])) {
            $context = $this->context();
            $manager = \Aimeos\M_Shop::create($context, 'locale/site');
            $search = $manager->filter()->add('locale.site.siteid', '=~', $current)->add('locale.site.status', '<', 1);
            $sites = $manager->search($search)->get_site_id();
            if (($site_id = (string) $context->user()?->get_site_id()) || $context->access('super')) {
                $sites = $sites->filter(fn($item) => strncmp($item, $site_id, strlen($site_id)));
            }
            self::$site_inactive[$current] = $sites;
        }
        return self::$site_inactive[$current];
    }
    /**
     * Returns the site ID that should be used based on the site level
     *
     * @param string $siteId Site ID to check
     * @param int $sitelevel Site level to check against
     * @return string Site ID that should be use based on the site level
     * @since 2022.04
     */
    protected function site_id(string $site_id, int $sitelevel): string
    {
        $sites = $this->context()->locale()->get_sites();
        if ($sitelevel & Locale::SITE_ONE && isset($sites[Locale::SITE_ONE]) && $site_id === $sites[Locale::SITE_ONE]) {
            return $site_id;
        }
        if ($sitelevel & Locale::SITE_PATH && isset($sites[Locale::SITE_PATH]) && in_array($site_id, $sites[Locale::SITE_PATH])) {
            return $site_id;
        }
        if ($sitelevel & Locale::SITE_SUBTREE && isset($sites[Locale::SITE_SUBTREE]) && !strncmp($sites[Locale::SITE_SUBTREE], $site_id, strlen($sites[Locale::SITE_SUBTREE]))) {
            return $site_id;
        }
        return $this->context()->locale()->get_site_id();
    }
    /**
     * Returns the site expression for the given name
     *
     * @param string $name SQL name for the site condition
     * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
     * @return string Site search condition
     * @since 2022.04
     */
    protected function site_string(string $name, int $sitelevel): string
    {
        $translation = ['marker' => $name];
        $types = ['marker' => \Aimeos\Base\DB\Statement\Base::PARAM_STR];
        return $this->site_condition('marker', $sitelevel)->to_source($types, $translation);
    }
}