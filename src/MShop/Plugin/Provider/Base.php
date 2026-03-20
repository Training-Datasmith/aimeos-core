<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider;

/**
 * Abstract class for plugin provider and decorator implementations
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class Base implements \Aimeos\Macro\Iface
{
    use \Aimeos\Macro\Macroable;
    private ?\Aimeos\M_Shop\Plugin\Provider\Iface $object = null;
    /**
     * Initializes the plugin instance.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
     */
    public function __construct(private \Aimeos\M_Shop\Context_Iface $context, private \Aimeos\M_Shop\Plugin\Item\Iface $item)
    {
    }
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
     */
    public function check_config_be(array $attributes): array
    {
        return [];
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return [];
    }
    /**
     * Injects the outer object into the decorator stack
     *
     * @param \Aimeos\MShop\Plugin\Provider\Iface $object First object of the decorator stack
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for chaining method calls
     */
    public function set_object(\Aimeos\M_Shop\Plugin\Provider\Iface $object): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $this->object = $object;
        return $this;
    }
    /**
     * Checks required fields and the types of the given data map
     *
     * @param array $criteria Multi-dimensional associative list of criteria configuration
     * @param array $map Values to check agains the criteria
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
     */
    protected function check_config(array $criteria, array $map): array
    {
        $helper = new \Aimeos\M_Shop\Common\Helper\Config\Standard($this->get_config_items($criteria));
        return $helper->check($map);
    }
    /**
     * Returns the criteria attribute items for the backend configuration
     *
     * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of criteria attribute items
     */
    protected function get_config_items(array $config_list): array
    {
        $list = [];
        foreach ($config_list as $key => $config) {
            $list[$key] = new \Aimeos\Base\Criteria\Attribute\Standard($config);
        }
        return $list;
    }
    /**
     * Returns the first object of the decorator stack
     *
     * @return \Aimeos\MShop\Plugin\Provider\Iface First object of the decorator stack
     */
    protected function object(): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        if ($this->object !== null) {
            return $this->object;
        }
        return $this;
    }
    /**
     * Returns the plugin item the provider is configured with.
     *
     * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item object
     */
    protected function get_item_base(): \Aimeos\M_Shop\Plugin\Item\Iface
    {
        return $this->item;
    }
    /**
     * Returns the configuration value from the service item specified by its key.
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if configuration key isn't available
     * @return mixed Value from service item configuration
     */
    protected function get_config_value(string $key, $default = null)
    {
        $config = $this->item->get_config();
        return $config[$key] ?? $default;
    }
    /**
     * Returns the context object.
     *
     * @return \Aimeos\MShop\ContextIface Context item object
     */
    protected function context(): \Aimeos\M_Shop\Context_Iface
    {
        return $this->context;
    }
}