<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Adds address and service items to the basket
 *
 * This plugins acts if a product is added to the basket or a delivery/payment
 * service is removed from the basket. It adds the a delivery/payment service
 * item and the customer address(es) to the basket.
 *
 * The following options are available:
 * - address: 1 (add billing address of the logged in customer to the basket)
 * - delivery: 1 (add the first delivery option to the basket)
 * - deliverycode: '...' and delivery: 1 (add specific delivery option to the basket)
 * - payment: 1 (add the first payment option to the basket)
 * - paymentcode: '...' and payment: 1 (add specific payment option to the basket)
 * - useorder: 1 (use last order of the customer to pre-fill addresses or services)
 * - orderservice: 1 (add delivery and payment services from the last order of the customer)
 * - orderaddress: 1 (add billing and delivery addresses from the last order of the customer)
 *
 * This plugin interacts with other plugins that add products or remove services!
 * Especially the "ServiceUpdate" plugin may remove a delivery/payment option
 * that isn't available any more based on the current basket content.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Autofill extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['address' => ['code' => 'address', 'internalcode' => 'address', 'label' => 'Add customer address automatically', 'type' => 'bool', 'default' => '', 'required' => false], 'delivery' => ['code' => 'delivery', 'internalcode' => 'delivery', 'label' => 'Add delivery option automatically', 'type' => 'bool', 'default' => '', 'required' => false], 'deliverycode' => ['code' => 'deliverycode', 'internalcode' => 'deliverycode', 'label' => 'Add delivery by code', 'default' => '', 'required' => false], 'payment' => ['code' => 'payment', 'internalcode' => 'payment', 'label' => 'Add payment option automatically', 'type' => 'bool', 'default' => '', 'required' => false], 'paymentcode' => ['code' => 'paymentcode', 'internalcode' => 'paymentcode', 'label' => 'Add payment by code', 'default' => '', 'required' => false], 'useorder' => ['code' => 'useorder', 'internalcode' => 'useorder', 'label' => 'Add from last order', 'type' => 'bool', 'default' => '', 'required' => false], 'orderaddress' => ['code' => 'orderaddress', 'internalcode' => 'orderaddress', 'label' => 'Add address from last order', 'type' => 'bool', 'default' => '', 'required' => false], 'orderservice' => ['code' => 'orderservice', 'internalcode' => 'orderservice', 'label' => 'Add delivery/payment from last order', 'type' => 'bool', 'default' => '', 'required' => false]];
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
        $p->attach($plugin, 'addAddress.after');
        $p->attach($plugin, 'setAddresses.after');
        $p->attach($plugin, 'addProduct.after');
        $p->attach($plugin, 'setProducts.after');
        $p->attach($plugin, 'deleteService.after');
        return $this;
    }
    /**
     * Receives a notification from a publisher object
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     * @throws \Aimeos\MShop\Plugin\Provider\Exception if an error occurs
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        $context = $this->context();
        $services = $order->get_services();
        $addresses = $order->get_addresses();
        if (($userid = $context->user()) !== null && (bool) $this->get_config_value('useorder', false) === true && ($addresses->is_empty() || $services->is_empty())) {
            $order_manager = \Aimeos\M_Shop::create($context, 'order');
            $search = $order_manager->filter()->add(['order.customerid' => $userid, 'order.languageid' => $order->locale()->get_language_id(), 'order.currencyid' => $order->locale()->get_currency_id()])->order('-order.id')->slice(0, 1);
            if (($item = $order_manager->search($search, ['order/address', 'order/service', 'service'])->first()) !== null) {
                $this->set_addresses($order, $item);
                $this->set_services($order, $item);
            }
        }
        $this->set_address_default($order);
        $this->set_services_default($order);
        return $value;
    }
    /**
     * Returns the order service item for the given type and code if available.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket of the customer
     * @param string $type Service type constant from \Aimeos\MShop\Order\Item\Service\Base
     * @param string|null $code Service item code
     * @return \Aimeos\MShop\Order\Item\Service\Iface|null Order service item if available or null otherwise
     */
    protected function get_service_item(\Aimeos\M_Shop\Order\Item\Iface $order, string $type, ?string $code = null): ?\Aimeos\M_Shop\Order\Item\Service\Iface
    {
        $context = $this->context();
        $service_manager = \Aimeos\M_Shop::create($context, 'service');
        $filter = $service_manager->filter(true)->add(['service.type' => $type])->order('service.position');
        if ($code !== null) {
            $filter->add('service.code', '==', $code);
        }
        foreach ($service_manager->search($filter, ['media', 'price', 'text']) as $item) {
            $provider = $service_manager->get_provider($item, $item->get_type());
            if ($provider->is_available($order) === true) {
                return \Aimeos\M_Shop::create($context, 'order/service')->create()->copy_from($item)->set_price($provider->calc_price($order));
            }
        }
        return null;
    }
    /**
     * Adds the addresses from the given order item to the basket.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @param \Aimeos\MShop\Order\Item\Iface $item Existing order to fetch the addresses from
     * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
     */
    protected function set_addresses(\Aimeos\M_Shop\Order\Item\Iface $order, \Aimeos\M_Shop\Order\Item\Iface $item): \Aimeos\M_Shop\Order\Item\Iface
    {
        if ($order->get_addresses()->is_empty() && (bool) $this->get_config_value('orderaddress', true) === true) {
            $map = $item->get_addresses();
            foreach ($map as $list) {
                map($list)->set_id(null);
            }
            $order->set_addresses($map);
        }
        return $order;
    }
    /**
     * Adds the services from the given order item to the basket.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @param \Aimeos\MShop\Order\Item\Iface $item Existing order to fetch the services from
     * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
     */
    protected function set_services(\Aimeos\M_Shop\Order\Item\Iface $order, \Aimeos\M_Shop\Order\Item\Iface $item): \Aimeos\M_Shop\Order\Item\Iface
    {
        if ($order->get_services()->is_empty() && $this->get_config_value('orderservice', true) == true) {
            $map = $item->get_services()->all();
            $service_manager = \Aimeos\M_Shop::create($this->context(), 'service');
            foreach ($map as $type => $list) {
                foreach ($list as $key => $service) {
                    if ($service_item = $service->get_service_item()) {
                        $provider = $service_manager->get_provider($service_item, $service->get_type());
                        if ($provider->is_available($order) === true) {
                            $attr_items = $service->get_attribute_items()->filter(fn($attr) => in_array($attr->get_type(), ['', 'hidden']));
                            $service->set_id(null)->set_attribute_items($attr_items->set_id(null));
                        } else {
                            unset($map[$type][$key]);
                        }
                    }
                }
            }
            $order->set_services($map);
        }
        return $order;
    }
    /**
     * Adds the default addresses to the basket if they are not available.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
     */
    protected function set_address_default(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        $context = $this->context();
        $addresses = $order->get_addresses();
        $type = \Aimeos\M_Shop\Order\Item\Address\Base::TYPE_PAYMENT;
        if ($context->user() !== null && !isset($addresses[$type]) && (bool) $this->get_config_value('address', false) === true) {
            $address = \Aimeos\M_Shop::create($context, 'customer')->get($context->user())->get_payment_address();
            $addr_item = \Aimeos\M_Shop::create($context, 'order/address')->create()->copy_from($address);
            $order->add_address($addr_item, $type);
        }
        return $order;
    }
    /**
     * Adds the default services to the basket if they are not available.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
     * @return \Aimeos\MShop\Order\Item\Iface Updated basket object
     */
    protected function set_services_default(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY;
        foreach ($order->get_service($type) as $pos => $service) {
            if ($this->get_service_item($order, $type, $service->get_code()) === null) {
                $order->delete_service($type, $pos);
            }
        }
        if ($order->get_service($type) === [] && (bool) $this->get_config_value('delivery', false) === true && (($item = $this->get_service_item($order, $type, $this->get_config_value('deliverycode'))) !== null || ($item = $this->get_service_item($order, $type)) !== null)) {
            $order->add_service($item, $type);
        }
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        foreach ($order->get_service($type) as $pos => $service) {
            if ($this->get_service_item($order, $type, $service->get_code()) === null) {
                $order->delete_service($type, $pos);
            }
        }
        if ($order->get_service($type) === [] && (bool) $this->get_config_value('payment', false) === true && (($item = $this->get_service_item($order, $type, $this->get_config_value('paymentcode'))) !== null || ($item = $this->get_service_item($order, $type)) !== null)) {
            $order->add_service($item, $type);
        }
        return $order;
    }
}