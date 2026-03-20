<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Adds the configured subscription product to the basket for free
 *
 * Sets the price of the configured product to 0.00 and uses it's price as rebate for
 * the configured number of times. This is bound to the e-mail address of the customer.
 *
 * The following options are available:
 * - productcode: '...' (SKU code of the product that should be available for free)
 * - count: ... (how often the product can be bought for free)
 *
 * @package MShop
 * @subpackage Plugin
 */
class Free_Product extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['productcode' => ['code' => 'productcode', 'internalcode' => 'productcode', 'label' => 'SKU of the free product', 'default' => '', 'required' => true], 'count' => ['code' => 'count', 'internalcode' => 'count', 'label' => 'Number of times the product is available for free', 'type' => 'int', 'default' => 1, 'required' => true]];
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
        $p->attach($this->object(), 'addProduct.after');
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
        map([$value])->implements(\Aimeos\M_Shop\Order\Item\Product\Iface::class, true);
        $code = $this->get_config_value('productcode');
        $addresses = $order->get_address(\Aimeos\M_Shop\Order\Item\Address\Base::TYPE_PAYMENT);
        if ($value->get_product_code() !== $code || ($address = current($addresses)) === false) {
            return $value;
        }
        $email = $address->get_email();
        $count = $this->get_config_value('count');
        $status = \Aimeos\M_Shop\Order\Item\Base::PAY_AUTHORIZED;
        $manager = \Aimeos\M_Shop::create($this->context(), 'order');
        $search = $manager->filter()->add(['order.address.email' => $email, 'order.product.prodcode' => $code])->add('order.statuspayment', '>=', $status);
        $result = $manager->aggregate($search, 'order.address.email', 'order.product.quantity', 'sum');
        if (isset($result[$email]) && $result[$email] < $count) {
            $value->set_price($value->get_price()->set_rebate($value->get_price()->get_value())->set_value('0.00'));
        }
        return $value;
    }
}