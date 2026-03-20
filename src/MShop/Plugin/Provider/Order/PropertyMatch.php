<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Checks the value of a property defined in the configuration
 *
 * Products can be only added to the basket if they contain the required
 * product properties.
 *
 * Example:
 * - values: {"mytype": "myvalue"}
 *
 * This configuration enforces products to have a size and color property.
 * Otherwise, they can't be added to the basket by the customers.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Property_Match extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['values' => ['code' => 'values', 'internalcode' => 'values', 'label' => 'Property type/value map', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => true]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        $errors = parent::check_config_be($attributes);
        return array_merge($errors, $this->check_config($this->be_config, $attributes));
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
     * Subscribes itself to a publisher
     *
     * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $p): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $plugin = $this->object();
        $p->attach($plugin, 'addProduct.before');
        $p->attach($plugin, 'setProducts.before');
        return $this;
    }
    /**
     * Receives a notification from a publisher object
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        if (($map = (array) $this->get_item_base()->get_config_value('values', [])) === []) {
            return $value;
        }
        $list = map($value);
        if ($this->get_product_items($list->get_product_id()->unique(), $map)->count() !== count($list)) {
            $code = ['product' => $map];
            $msg = $this->context()->translate('mshop', 'Product matching given properties not found');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception($msg, -1, null, $code);
        }
        return $value;
    }
    /**
     * Returns the product items for the given product IDs limited by the map of properties
     *
     * @param iterable $productIds List of product IDs
     * @param array $map Assoicative list of property types as keys and property values
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Product\Item\Iface with IDs as keys
     */
    protected function get_product_items(iterable $product_ids, array $map): \Aimeos\Map
    {
        $context = $this->context();
        $lang_id = $context->locale()->get_language_id();
        $manager = \Aimeos\M_Shop::create($context, 'product');
        $search = $manager->filter(true);
        $expr = [$search->is('product.id', '==', $product_ids)];
        foreach ($map as $type => $value) {
            $func = $search->make('product:prop', [$type, [$lang_id, null], (string) $value]);
            $expr[] = $search->is($func, '!=', null);
        }
        $search->add($search->and($expr));
        return $manager->search($search);
    }
}