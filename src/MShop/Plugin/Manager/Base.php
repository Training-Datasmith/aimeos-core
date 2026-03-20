<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Manager;

/**
 * Abstract class for plugin managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base extends \Aimeos\M_Shop\Common\Manager\Base
{
    private array $plugins = [];
    /**
     * Returns the plugin provider which is responsible for the plugin item.
     *
     * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
     * @param string $type Plugin type code
     * @return \Aimeos\MShop\Plugin\Provider\Iface Returns the decoratad plugin provider object
     * @throws \LogicException If provider couldn't be found
     */
    public function get_provider(\Aimeos\M_Shop\Plugin\Item\Iface $item, string $type): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $type = ucwords($type);
        $context = $this->context();
        $names = explode(',', $item->get_provider());
        if (ctype_alnum($type) === false) {
            throw new \LogicException(sprintf('Invalid characters in type name "%1$s"', $type), 400);
        }
        if (($provider = array_shift($names)) === null) {
            throw new \LogicException(sprintf('Provider in "%1$s" not available', $item->get_provider()), 400);
        }
        if (ctype_alnum($provider) === false) {
            throw new \LogicException(sprintf('Invalid characters in provider name "%1$s"', $provider), 400);
        }
        $classname = '\Aimeos\MShop\Plugin\Provider\\' . $type . '\\' . $provider;
        $interface = \Aimeos\M_Shop\Plugin\Provider\Factory\Iface::class;
        $provider = \Aimeos\Utils::create($classname, [$context, $item], $interface);
        /** mshop/plugin/provider/order/decorators
         * Adds a list of decorators to all order plugin provider objects automatcally
         *
         * Decorators extend the functionality of a class by adding new aspects
         * (e.g. log what is currently done), executing the methods of the underlying
         * class only in certain conditions (e.g. only for logged in users) or
         * modify what is returned to the caller.
         *
         * This option allows you to wrap decorators
         * ("\Aimeos\MShop\Plugin\Provider\Decorator\*") around the order provider.
         *
         *  mshop/plugin/provider/order/decorators = array( 'decorator1' )
         *
         * This would add the decorator named "decorator1" defined by
         * "\Aimeos\MShop\Plugin\Provider\Decorator\Decorator1" to all order provider
         * objects.
         *
         * @param array List of decorator names
         * @since 2014.03
         * @see mshop/plugin/provider/order/decorators
         */
        $decorators = $context->config()->get('mshop/plugin/provider/' . $item->get_type() . '/decorators', []);
        $provider = $this->add_plugin_decorators($item, $provider, $names);
        $provider = $this->add_plugin_decorators($item, $provider, $decorators);
        return $provider->set_object($provider);
    }
    /**
     * Registers plugins to the given publisher.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $publisher Publisher object
     * @param string $type Unique plugin type code
     * @return \Aimeos\MShop\Plugin\Manager\Iface Manager object for chaining method calls
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $publisher, string $type): \Aimeos\M_Shop\Plugin\Manager\Iface
    {
        if (!isset($this->plugins[$type])) {
            $search = $this->object()->filter(true);
            $expr = [$search->compare('==', 'plugin.type', $type), $search->get_conditions()];
            $search->set_conditions($search->and($expr));
            $search->set_sortations([$search->sort('+', 'plugin.position')]);
            $this->plugins[$type] = [];
            foreach ($this->object()->search($search) as $item) {
                $this->plugins[$type][$item->get_id()] = $this->get_provider($item, $type);
            }
        }
        foreach ($this->plugins[$type] as $plugin) {
            $plugin->register($publisher);
        }
        return $this;
    }
    /**
     *
     * @param \Aimeos\MShop\Plugin\Item\Iface $pluginItem Plugin item object
     * @param \Aimeos\MShop\Plugin\Provider\Iface $provider Plugin provider object
     * @param array $names List of decorator names that should be wrapped around the plugin provider object
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin provider object
     */
    protected function add_plugin_decorators(\Aimeos\M_Shop\Plugin\Item\Iface $plugin_item, \Aimeos\M_Shop\Plugin\Provider\Iface $provider, array $names): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $context = $this->context();
        $classprefix = '\Aimeos\MShop\Plugin\Provider\Decorator\\';
        foreach ($names as $name) {
            if (ctype_alnum($name) === false) {
                $msg = $context->translate('mshop', 'Invalid characters in class name "%1$s"');
                throw new \Aimeos\M_Shop\Plugin\Exception(sprintf($msg, $name));
            }
            $classname = $classprefix . $name;
            $interface = \Aimeos\M_Shop\Plugin\Provider\Decorator\Iface::class;
            $provider = \Aimeos\Utils::create($classname, [$context, $plugin_item, $provider], $interface);
        }
        return $provider;
    }
}