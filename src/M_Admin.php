<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 */
namespace Aimeos;

/**
 * Factory which can create all MAdmin managers
 *
 * @package MAdmin
 */
class M_Admin
{
    private static ?\Aimeos\M_Shop\Context_Iface $context = null;
    private static bool $cache = true;
    private static array $objects = [];
    /**
     * Enables or disables caching of class instances and clears cache
     *
     * @param bool $value True to enable caching, false to disable it
     */
    public static function cache(bool $value): void
    {
        self::$cache = $value;
        self::$context = null;
        self::$objects = [];
    }
    /**
     * Creates the required manager specified by the given path of manager names
     *
     * Domain managers are created by providing only the domain name, e.g.
     * "product" for the \Aimeos\MAdmin\Log\Manager\Standard or a path of names to
     * retrieve a specific sub-manager.
     * Please note, that only the default managers can be created. If you need
     * a specific implementation, you need to use the factory class of the
     * domain or the getSubManager() method to hand over specifc implementation
     * names.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object required by managers
     * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "log"
     * @param string|null $name Name of the controller implementation ("Standard" if null)
     * @return \Aimeos\MShop\Common\Manager\Iface MAdmin manager object
     * @throws \Aimeos\MAdmin\Exception If the given path is invalid or the manager wasn't found
     */
    public static function create(\Aimeos\M_Shop\Context_Iface $context, string $path, ?string $name = null): \Aimeos\M_Shop\Common\Manager\Iface
    {
        if (empty($path)) {
            throw new \LogicException('Manager path is empty', 400);
        }
        if (preg_match('/^[a-z0-9\/]+$/', $path) !== 1) {
            throw new \LogicException(sprintf('Invalid component path "%1$s"', $path, 400));
        }
        if (self::$context !== null && self::$context !== $context) {
            self::$objects = [];
            // clear cached objects on context change
        }
        self::$context = $context;
        $parts = explode('/', $path);
        if (($domain = array_shift($parts)) === null) {
            throw new \LogicException(sprintf('Manager path is empty', $path), 400);
        }
        if (empty($name)) {
            $name = $context->config()->get('madmin/' . $domain . '/manager/name', 'Standard');
        }
        $classname = '\Aimeos\MAdmin\\' . ucfirst($domain) . '\Manager\\' . $name;
        if (self::$cache === false || !isset(self::$objects[$classname])) {
            $iface = '\Aimeos\MAdmin\\' . ucfirst($domain) . '\Manager\Iface';
            $manager = self::create_manager($context, $classname, interface_exists($iface) ? $iface : null);
            $manager = self::add_manager_decorators($context, $manager, $domain);
            self::$objects[$classname] = $manager;
        }
        return self::$objects[$classname];
    }
    /**
     * Injects a manager object for the given path of manager names
     *
     * This method is for testing only and you must call \Aimeos\MAdmin::cache( false )
     * afterwards!
     *
     * @param string $classname Full name of the class for which the object should be returned
     * @param \Aimeos\MShop\Common\Manager\Iface|null $object Manager object for the given manager path or null to clear
     */
    public static function inject(string $classname, ?\Aimeos\M_Shop\Common\Manager\Iface $object = null): void
    {
        self::$objects['\\' . ltrim($classname, '\\')] = $object;
    }
    /**
     * Adds the decorators to the manager object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
     * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
     * @param array $decorators List of decorator names that should be wrapped around the manager object
     * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\MShop\Product\Manager\Decorator\"
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object
     * @throws \LogicException If class isn't found
     */
    protected static function add_decorators(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Common\Manager\Iface $manager, array $decorators, string $classprefix): \Aimeos\M_Shop\Common\Manager\Iface
    {
        $interface = \Aimeos\M_Shop\Common\Manager\Decorator\Iface::class;
        foreach ($decorators as $name) {
            if (ctype_alnum($name) === false) {
                $classname = is_string($name) ? $classprefix . $name : '<not a string>';
                throw new \LogicException(sprintf('Invalid class name "%1$s"', $classname), 400);
            }
            $classname = $classprefix . $name;
            $manager = \Aimeos\Utils::create($classname, [$manager, $context], $interface);
        }
        return $manager;
    }
    /**
     * Adds the decorators to the manager object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
     * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
     * @param string $domain Domain name in lower case, e.g. "product"
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object
     */
    protected static function add_manager_decorators(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Common\Manager\Iface $manager, string $domain): \Aimeos\M_Shop\Common\Manager\Iface
    {
        $config = $context->config();
        $classprefix = '\Aimeos\MShop\\' . ucfirst($domain) . '\Manager\Decorator\\';
        $decorators = array_reverse($config->get('madmin/' . $domain . '/manager/decorators/local', []));
        $manager = self::add_decorators($context, $manager, $decorators, $classprefix);
        $classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
        $decorators = array_reverse($config->get('madmin/' . $domain . '/manager/decorators/global', []));
        $manager = self::add_decorators($context, $manager, $decorators, $classprefix);
        /** madmin/common/manager/decorators/default
         * Configures the list of decorators applied to all admin managers
         *
         * Decorators extend the functionality of a class by adding new aspects
         * (e.g. log what is currently done), executing the methods of the underlying
         * class only in certain conditions (e.g. only for logged in users) or
         * modify what is returned to the caller.
         *
         * This option allows you to configure a list of decorator names that should
         * be wrapped around the original instances of all created managers:
         *
         *  madmin/common/manager/decorators/default = array( 'decorator1', 'decorator2' )
         *
         * This would wrap the decorators named "decorator1" and "decorator2" around
         * all controller instances in that order. The decorator classes would be
         * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" and
         * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2".
         *
         * @param array List of decorator names
         * @since 2014.03
         */
        $decorators = array_reverse($config->get('madmin/common/manager/decorators/default', []));
        $excludes = $config->get('madmin/' . $domain . '/manager/decorators/excludes', []);
        foreach ($decorators as $key => $name) {
            if (in_array($name, $excludes)) {
                unset($decorators[$key]);
            }
        }
        $classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
        return self::add_decorators($context, $manager, $decorators, $classprefix);
    }
    /**
     * Creates a manager object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
     * @param string $classname Name of the manager class
     * @param string $interface Name of the manager interface
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object
     */
    protected static function create_manager(\Aimeos\M_Shop\Context_Iface $context, string $classname, ?string $interface): \Aimeos\M_Shop\Common\Manager\Iface
    {
        return self::$objects[$classname] ?? \Aimeos\Utils::create($classname, [$context], $interface);
    }
}