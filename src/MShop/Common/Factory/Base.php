<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Factory;

/**
 * Common methods for all factories.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
{
    private static array $objects = [];
    /**
     * Injects a manager object.
     * The object is returned via createManager() if an instance of the class
     * with the name name is requested.
     *
     * @param string $classname Full name of the class for which the object should be returned
     * @param \Aimeos\MShop\Common\Manager\Iface|null $manager Manager object or null for removing the manager object
     */
    public static function inject_manager(string $classname, ?\Aimeos\M_Shop\Common\Manager\Iface $manager = null): void
    {
        self::$objects[$classname] = $manager;
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
        foreach ($decorators as $name) {
            if (ctype_alnum($name) === false) {
                throw new \LogicException(sprintf('Invalid characters in class name "%1$s"', $name), 400);
            }
            $classname = $classprefix . $name;
            $interface = \Aimeos\M_Shop\Common\Manager\Decorator\Iface::class;
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
        /** mshop/common/manager/decorators/default
         * Configures the list of decorators applied to all shop managers
         *
         * Decorators extend the functionality of a class by adding new aspects
         * (e.g. log what is currently done), executing the methods of the underlying
         * class only in certain conditions (e.g. only for logged in users) or
         * modify what is returned to the caller.
         *
         * This option allows you to configure a list of decorator names that should
         * be wrapped around the original instances of all created managers:
         *
         *  mshop/common/manager/decorators/default = array( 'decorator1', 'decorator2' )
         *
         * This would wrap the decorators named "decorator1" and "decorator2" around
         * all controller instances in that order. The decorator classes would be
         * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" and
         * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2".
         *
         * @param array List of decorator names
         * @since 2014.03
         */
        $decorators = $config->get('mshop/common/manager/decorators/default', []);
        $excludes = $config->get('mshop/' . $domain . '/manager/decorators/excludes', []);
        foreach ($decorators as $key => $name) {
            if (in_array($name, $excludes)) {
                unset($decorators[$key]);
            }
        }
        $classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
        $manager = self::add_decorators($context, $manager, $decorators, $classprefix);
        $classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
        $decorators = $config->get('mshop/' . $domain . '/manager/decorators/global', []);
        $manager = self::add_decorators($context, $manager, $decorators, $classprefix);
        $classprefix = '\Aimeos\MShop\\' . ucfirst($domain) . '\Manager\Decorator\\';
        $decorators = $config->get('mshop/' . $domain . '/manager/decorators/local', []);
        $manager = self::add_decorators($context, $manager, $decorators, $classprefix);
        return $manager->set_object($manager);
    }
    /**
     * Creates a manager object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
     * @param string $classname Name of the manager class
     * @param string $interface Name of the manager interface
     * @return \Aimeos\MShop\Common\Manager\Iface Manager object
     */
    protected static function create_manager(\Aimeos\M_Shop\Context_Iface $context, string $classname, string $interface): \Aimeos\M_Shop\Common\Manager\Iface
    {
        return self::$objects[$classname] ?? \Aimeos\Utils::create($classname, [$context], $interface);
    }
}