<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Base decorator methods for service provider.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base extends \Aimeos\M_Shop\Service\Provider\Base
{
    /**
     * Initializes a new service provider object using the given context object.
     *
     * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider or decorator
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
     */
    public function __construct(private \Aimeos\M_Shop\Service\Provider\Iface $provider, \Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Service\Item\Iface $service_item)
    {
        parent::__construct($context, $service_item);
    }
    /**
     * Returns the price when using the provider.
     * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
     * the basket content, e.g. 2% of the value as transaction cost.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @param array $options Selected options by customer from frontend
     * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
     */
    public function calc_price(\Aimeos\M_Shop\Order\Item\Iface $basket, array $options = []): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->provider->calc_price($basket, $options);
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
     * Checks the frontend configuration attributes for validity.
     *
     * @param array $attributes Attributes entered by the customer during the checkout process
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_fe(array $attributes): array
    {
        return $this->provider->check_config_fe($attributes);
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
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the frontend.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_fe(\Aimeos\M_Shop\Order\Item\Iface $basket): array
    {
        return $this->provider->get_config_fe($basket);
    }
    /**
     * Injects additional global configuration for the backend.
     *
     * It's used for adding additional backend configuration from the application
     * like the URLs to redirect to.
     *
     * Supported redirect URLs are:
     * - payment.url-success
     * - payment.url-failure
     * - payment.url-cancel
     * - payment.url-update
     *
     * @param array $config Associative list of config keys and their value
     */
    public function inject_global_config_be(array $config): \Aimeos\M_Shop\Service\Provider\Iface
    {
        parent::inject_global_config_be($config);
        $this->provider->inject_global_config_be($config);
        return $this;
    }
    /**
     * Checks if payment provider can be used based on the basket content.
     * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        return $this->provider->is_available($basket);
    }
    /**
     * Checks what features the payment provider implements.
     *
     * @param int $what Constant from abstract class
     * @return bool True if feature is available in the payment provider, false if not
     */
    public function is_implemented(int $what): bool
    {
        return $this->provider->is_implemented($what);
    }
    /**
     * Cancels the authorization for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function cancel(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->provider->cancel($order);
    }
    /**
     * Captures the money later on request for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function capture(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->provider->capture($order);
    }
    /**
     * Processes the order
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object to process
     * @param array $params Request parameter if available
     * @return \Aimeos\MShop\Common\Helper\Form\Standard|null Form object or null
     */
    public function process(\Aimeos\M_Shop\Order\Item\Iface $order, array $params = []): ?\Aimeos\M_Shop\Common\Helper\Form\Iface
    {
        return $this->provider->process($order, $params);
    }
    /**
     * Sends the details of all orders to the ERP system for further processing
     *
     * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
     * @return \Aimeos\Map Updated order item objects
     */
    public function push(iterable $orders): \Aimeos\Map
    {
        return $this->provider->push($orders);
    }
    /**
     * Refunds the money for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function refund(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->provider->refund($order);
    }
    /**
     * Executes the payment again for the given order if supported.
     *
     * This requires support of the payment gateway and token based payment
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function repay(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->provider->repay($order);
    }
    /**
     * Queries for status updates for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function query(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->provider->query($order);
    }
    /**
     * Sets the payment attributes in the given service.
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem Order service item that will be added to the basket
     * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item with attributes added
     */
    public function set_config_fe(\Aimeos\M_Shop\Order\Item\Service\Iface $order_service_item, array $attributes): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->provider->set_config_fe($order_service_item, $attributes);
    }
    /**
     * Looks for new update files and updates the orders for which status updates were received.
     * If batch processing of files isn't supported, this method can be empty.
     *
     * @return bool True if the update was successful, false if async updates are not supported
     * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
     */
    public function update_async(): bool
    {
        return $this->provider->update_async();
    }
    /**
     * Updates the order status sent by payment gateway notifications
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Response object
     */
    public function update_push(\Psr\Http\Message\Server_Request_Interface $request, \Psr\Http\Message\Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        return $this->provider->update_push($request, $response);
    }
    /**
     * Updates the orders for whose status updates have been received by the confirmation page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object with parameters and request body
     * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item that should be updated
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item
     * @throws \Aimeos\MShop\Service\Exception If updating the orders failed
     */
    public function update_sync(\Psr\Http\Message\Server_Request_Interface $request, \Aimeos\M_Shop\Order\Item\Iface $order_item): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $this->provider->update_sync($request, $order_item);
    }
    /**
     * Returns the provider object.
     *
     * @return \Aimeos\MShop\Service\Provider\Iface Service provider object
     */
    protected function get_provider(): \Aimeos\M_Shop\Service\Provider\Iface
    {
        return $this->provider;
    }
    /**
     * Passes unknown methods to wrapped objects.
     *
     * @param string $name Name of the method
     * @param array $param List of method parameter
     * @return mixed Returns the value of the called method
     * @throws \Aimeos\MShop\Service\Exception If method call failed
     */
    public function __call(string $name, array $param)
    {
        return @call_user_func_array([$this->provider, $name], $param);
    }
}