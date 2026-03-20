<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Decorator;

/**
 * Base decorator methods for plugin provider.
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class Base extends \Aimeos\M_Shop\Plugin\Provider\Base
{
    /**
     * Initializes the plugin instance
     *
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
     * @param \Aimeos\MShop\Plugin\Provider\Iface $provider Plugin provider object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Plugin\Item\Iface $item, private \Aimeos\M_Shop\Plugin\Provider\Iface $provider)
    {
        parent::__construct($context, $item);
    }
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        return $this->provider->check_config_be($attributes);
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return $this->provider->get_config_be();
    }
    /**
     * Subscribes itself to a publisher
     *
     * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $p): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $this->provider->register($p);
        return $this;
    }
    /**
     * Receives a notification from a publisher object
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        return $this->provider->update($order, $action, $value);
    }
    /**
     * Injects the outmost object into the decorator stack
     *
     * @param \Aimeos\MShop\Plugin\Provider\Iface $object First object of the decorator stack
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for chaining method calls
     */
    public function set_object(\Aimeos\M_Shop\Plugin\Provider\Iface $object): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        parent::set_object($object);
        $this->provider->set_object($object);
        return $this;
    }
    /**
     * Returns the next provider or decorator.
     *
     * @return \Aimeos\MShop\Plugin\Provider\Iface Provider or decorator object
     */
    protected function get_provider(): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        return $this->provider;
    }
}