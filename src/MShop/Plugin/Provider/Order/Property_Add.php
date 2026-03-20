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
 * Adds product properties to an order product as attributes
 *
 * Example configuration:
 * - types: ["package-length", "package-width", "package-height", "package-weight"]
 *
 * The product properties listed in the array are added to the order product as
 * order product attributes with key/value pairs like code: "package-length", value: "10".
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Property_Add extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['types' => ['code' => 'types', 'internalcode' => 'types', 'label' => 'Property type codes', 'type' => 'list', 'internaltype' => 'array', 'default' => [], 'required' => true]];
    private \Aimeos\M_Shop\Common\Manager\Iface $order_attr_manager;
    /**
     * Initializes the plugin instance
     *
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Plugin\Item\Iface $item)
    {
        parent::__construct($context, $item);
        $this->order_attr_manager = \Aimeos\M_Shop::create($context, 'order/product/attribute');
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
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        if (($types = (array) $this->get_item_base()->get_config_value('types', [])) === []) {
            return $value;
        }
        $map = map($value);
        $products = $this->get_product_items($map->get_product_id()->unique()->all());
        if (!is_array($value)) {
            return $this->add_attributes($value, $products, $types);
        }
        foreach ($value as $key => $order_product) {
            $value[$key] = $this->add_attributes($order_product, $products, $types);
        }
        return $value;
    }
    /**
     * Adds the product properties as attribute items to the order product item
     *
     * @param \Aimeos\MShop\Order\Item\Product\Iface $orderProduct Order product containing attributes
     * @param \Aimeos\Map $products List of items implementing \Aimeos\MShop\Product\Item\Iface with IDs as keys and properties
     * @param string[] $types List of property types to add
     * @return \Aimeos\MShop\Order\Item\Product\Iface Modified order product item
     */
    protected function add_attributes(\Aimeos\M_Shop\Order\Item\Product\Iface $order_product, \Aimeos\Map $products, array $types): \Aimeos\M_Shop\Order\Item\Product\Iface
    {
        if (($product = $products->get($order_product->get_product_id())) === null) {
            return $order_product;
        }
        foreach ($types as $type) {
            $list = $product->get_properties($type);
            if (!$list->is_empty()) {
                if (($attr_item = $order_product->get_attribute_item($type, 'product/property')) === null) {
                    $attr_item = $this->order_attr_manager->create();
                }
                $attr_item = $attr_item->set_type('product/property')->set_code($type)->set_value(count($list) > 1 ? $list->to_array() : $list->first());
                $order_product = $order_product->set_attribute_item($attr_item);
            }
        }
        return $order_product;
    }
    /**
     * Returns the product items for the given product IDs limited by the map of properties
     *
     * @param string[] $productIds List of product IDs
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Product\Item\Iface with IDs as keys
     */
    protected function get_product_items(array $product_ids): \Aimeos\Map
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'product');
        $search = $manager->filter(true)->add(['product.id' => $product_ids]);
        return $manager->search($search, ['product/property']);
    }
}