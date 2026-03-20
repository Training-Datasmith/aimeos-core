<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Provider;

/**
 * Abstract class for rule provider and decorator implementations
 *
 * @package MShop
 * @subpackage Rule
 */
abstract class Base implements \Aimeos\Macro\Iface
{
    use \Aimeos\Macro\Macroable;
    private ?\Aimeos\M_Shop\Rule\Provider\Iface $object = null;
    private array $be_config = ['last-rule' => ['code' => 'last-rule', 'internalcode' => 'last-rule', 'label' => 'Don\'t execute subsequent rules', 'type' => 'bool', 'default' => 0, 'required' => false]];
    /**
     * Initializes the rule instance.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
     */
    public function __construct(private \Aimeos\M_Shop\Context_Iface $context, private \Aimeos\M_Shop\Rule\Item\Iface $item)
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
        return $this->check_config($this->be_config, $attributes);
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return $this->get_config_items($this->be_config);
    }
    /**
     * Injects the outer object into the decorator stack
     *
     * @param \Aimeos\MShop\Rule\Provider\Iface $object First object of the decorator stack
     * @return \Aimeos\MShop\Rule\Provider\Iface Rule object for chaining method calls
     */
    public function set_object(\Aimeos\M_Shop\Rule\Provider\Iface $object): \Aimeos\M_Shop\Rule\Provider\Iface
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
     * @return \Aimeos\MShop\Rule\Provider\Iface First object of the decorator stack
     */
    protected function object(): \Aimeos\M_Shop\Rule\Provider\Iface
    {
        if ($this->object !== null) {
            return $this->object;
        }
        return $this;
    }
    /**
     * Returns the rule item the provider is configured with.
     *
     * @return \Aimeos\MShop\Rule\Item\Iface Rule item object
     */
    protected function get_item_base(): \Aimeos\M_Shop\Rule\Item\Iface
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
    /**
     * Tests if this rule should be the last one that is applied
     *
     * @return bool True, if rule should be the last one, false to continue with further rules
     */
    protected function is_last(): bool
    {
        return (bool) $this->get_config_value('last-rule', false);
    }
}