<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Payment;

/**
 * Payment provider for paypal express orders.
 *
 * @package MShop
 * @subpackage Service
 */
class Pay_Pal_Express extends \Aimeos\M_Shop\Service\Provider\Payment\Base implements \Aimeos\M_Shop\Service\Provider\Payment\Iface
{
    private string $apiendpoint;
    private array $be_config = ['paypalexpress.ApiUsername' => ['code' => 'paypalexpress.ApiUsername', 'internalcode' => 'paypalexpress.ApiUsername', 'label' => 'NVP API Username', 'default' => '', 'required' => true], 'paypalexpress.AccountEmail' => ['code' => 'paypalexpress.AccountEmail', 'internalcode' => 'paypalexpress.AccountEmail', 'label' => 'Registered e-mail address of the shop owner in PayPal', 'default' => '', 'required' => true], 'paypalexpress.ApiPassword' => ['code' => 'paypalexpress.ApiPassword', 'internalcode' => 'paypalexpress.ApiPassword', 'label' => 'NVP API Password', 'default' => '', 'required' => true], 'paypalexpress.ApiSignature' => ['code' => 'paypalexpress.ApiSignature', 'internalcode' => 'paypalexpress.ApiSignature', 'label' => 'NVP API Signature', 'default' => '', 'required' => true], 'paypalexpress.ApiEndpoint' => ['code' => 'paypalexpress.ApiEndpoint', 'internalcode' => 'paypalexpress.ApiEndpoint', 'label' => 'NVP API API Endpoint', 'default' => 'https://api-3t.paypal.com/nvp', 'required' => true], 'paypalexpress.PaypalUrl' => ['code' => 'paypalexpress.PaypalUrl', 'internalcode' => 'paypalexpress.PaypalUrl', 'label' => 'NVP Express Checkout Url', 'default' => 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s', 'required' => true], 'paypalexpress.url-validate' => ['code' => 'paypalexpress.url-validate', 'internalcode' => 'paypalexpress.url-validate', 'label' => 'NVP Validation URL', 'default' => 'https://www.paypal.com/webscr&cmd=_notify-validate', 'required' => true], 'paypalexpress.PaymentAction' => ['code' => 'paypalexpress.PaymentAction', 'internalcode' => 'paypalexpress.PaymentAction', 'label' => 'How to obtain the payment: "Sale" (final sale), "Authorization" (basic authoriziation and capture) or "Order" (order authoriziation and capture)', 'default' => 'Sale', 'required' => true], 'paypalexpress.LandingPage' => ['code' => 'paypalexpress.LandingPage', 'internalcode' => 'paypalexpress.LandingPage', 'label' => 'Type of displayed PayPal page: "Login" (PayPal login) or "Billing" (Non-PayPal account)', 'default' => 'Login', 'required' => false], 'paypalexpress.FundingSource' => ['code' => 'paypalexpress.FundingSource', 'internalcode' => 'paypalexpress.FundingSource', 'label' => 'Preferred payment option: "CreditCard", "ELV", "ChinaUnionPay" or "QIWI" ("paypalexpress.LandingPage" must be set to "Billing")', 'default' => 'CreditCard', 'required' => false], 'paypalexpress.LocaleCode' => ['code' => 'paypalexpress.LocaleCode', 'internalcode' => 'paypalexpress.LocaleCode', 'label' => 'ISO language code used at the PayPal page', 'default' => '', 'required' => false], 'paypalexpress.AddrOverride' => ['code' => 'paypalexpress.AddrOverride', 'internalcode' => 'paypalexpress.AddrOverride', 'label' => 'Customer can change address', 'type' => 'bool', 'default' => 0, 'required' => false], 'paypalexpress.NoShipping' => ['code' => 'paypalexpress.NoShipping', 'internalcode' => 'paypalexpress.NoShipping', 'label' => 'Don\'t display shipping address', 'type' => 'bool', 'default' => 1, 'required' => false], 'paypalexpress.address' => ['code' => 'paypalexpress.address', 'internalcode' => 'paypalexpress.address', 'label' => 'Pass customer address to PayPal', 'type' => 'bool', 'default' => 1, 'required' => false], 'paypalexpress.product' => ['code' => 'paypalexpress.product', 'internalcode' => 'paypalexpress.product', 'label' => 'Pass product details to PayPal', 'type' => 'bool', 'default' => 1, 'required' => false], 'paypalexpress.service' => ['code' => 'paypalexpress.service', 'internalcode' => 'paypalexpress.service', 'label' => 'Pass delivery/payment details to PayPal', 'type' => 'bool', 'default' => 1, 'required' => false]];
    /**
     * Initializes the provider object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration
     * @throws \Aimeos\MShop\Service\Exception If one of the required configuration values isn't available
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Service\Item\Iface $service_item)
    {
        parent::__construct($context, $service_item);
        $default = 'https://api-3t.paypal.com/nvp';
        $this->apiendpoint = $this->get_config_value(['paypalexpress.ApiEndpoint'], $default);
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
     * Tries to get an authorization or captures the money immediately for the given order if capturing the money
     * separately isn't supported or not configured by the shop owner.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @param array $params Request parameter if available
     * @return \Aimeos\MShop\Common\Helper\Form\Iface|null Form object with URL, action and parameters to redirect to
     * 	(e.g. to an external server of the payment provider or to a local success page)
     */
    public function process(\Aimeos\M_Shop\Order\Item\Iface $order, array $params = []): ?\Aimeos\M_Shop\Common\Helper\Form\Iface
    {
        $values = $this->get_order_details($order);
        $values['METHOD'] = 'SetExpressCheckout';
        $values['PAYMENTREQUEST_0_INVNUM'] = $order->get_id();
        $values['RETURNURL'] = $this->get_config_value(['payment.url-success']);
        $values['CANCELURL'] = $this->get_config_value(['payment.url-cancel', 'payment.url-success']);
        $values['USERSELECTEDFUNDINGSOURCE'] = $this->get_config_value(['paypalexpress.FundingSource'], 'CreditCard');
        $values['LANDINGPAGE'] = $this->get_config_value(['paypalexpress.LandingPage'], 'Login');
        $url_query = http_build_query($values, '', '&');
        $response = $this->send($this->apiendpoint, 'POST', $url_query);
        $rvals = $this->check_response($order->get_id(), $response, __METHOD__);
        $default = 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s';
        $paypal_url = sprintf($this->get_config_value(['paypalexpress.PaypalUrl'], $default), $rvals['TOKEN']);
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        $service_item = $this->get_basket_service($order, $type, $this->get_service_item()->get_code());
        $service_item->add_attribute_items($this->attributes(['TOKEN' => $rvals['TOKEN']], 'tx'));
        return new \Aimeos\M_Shop\Common\Helper\Form\Standard($paypal_url, 'POST', []);
    }
    /**
     * Queries for status updates for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function query(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        if (($tid = $this->get_order_service_item($order)->get_attribute('TRANSACTIONID', 'tx')) === null) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $order->get_id()));
        }
        $values = $this->get_auth_parameter();
        $values['METHOD'] = 'GetTransactionDetails';
        $values['TRANSACTIONID'] = $tid;
        $url_query = http_build_query($values, '', '&');
        $response = $this->send($this->apiendpoint, 'POST', $url_query);
        $rvals = $this->check_response($order->get_id(), $response, __METHOD__);
        return $this->set_status_payment($order, $rvals);
    }
    /**
     * Captures the money later on request for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function capture(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        $service_item = $this->get_basket_service($order, $type, $this->get_service_item()->get_code());
        if (($tid = $service_item->get_attribute('TRANSACTIONID', 'tx')) === null) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $order->get_id()));
        }
        $price = $order->get_price();
        $values = $this->get_auth_parameter();
        $values['METHOD'] = 'DoCapture';
        $values['COMPLETETYPE'] = 'Complete';
        $values['AUTHORIZATIONID'] = $tid;
        $values['INVNUM'] = $order->get_id();
        $values['CURRENCYCODE'] = $price->get_currency_id();
        $values['AMT'] = $this->get_amount($price);
        $url_query = http_build_query($values, '', '&');
        $response = $this->send($this->apiendpoint, 'POST', $url_query);
        $rvals = $this->check_response($order->get_id(), $response, __METHOD__);
        $this->set_status_payment($order, $rvals);
        $attributes = [];
        if (isset($rvals['PARENTTRANSACTIONID'])) {
            $attributes['PARENTTRANSACTIONID'] = $rvals['PARENTTRANSACTIONID'];
        }
        // updates the transaction id
        $attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
        $service_item->add_attribute_items($this->attributes($attributes, 'tx'));
        return $order;
    }
    /**
     * Refunds the money for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @param \Aimeos\MShop\Price\Item\Iface|null $price Price item with the amount to refund or NULL for whole order
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function refund(\Aimeos\M_Shop\Order\Item\Iface $order, ?\Aimeos\M_Shop\Price\Item\Iface $price = null): \Aimeos\M_Shop\Order\Item\Iface
    {
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        $service_item = $this->get_basket_service($order, $type, $this->get_service_item()->get_code());
        if (($tid = $service_item->get_attribute('TRANSACTIONID', 'tx')) === null) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $order->get_id()));
        }
        $values = $this->get_auth_parameter();
        $values['METHOD'] = 'RefundTransaction';
        $values['REFUNDSOURCE'] = 'instant';
        $values['REFUNDTYPE'] = 'Full';
        $values['TRANSACTIONID'] = $tid;
        $values['INVOICEID'] = $order->get_id();
        $url_query = http_build_query($values, '', '&');
        $response = $this->send($this->apiendpoint, 'POST', $url_query);
        $rvals = $this->check_response($order->get_id(), $response, __METHOD__);
        $attributes = ['REFUNDTRANSACTIONID' => $rvals['REFUNDTRANSACTIONID']];
        $service_item->add_attribute_items($this->attributes($attributes, 'tx'));
        return $order->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_REFUND);
    }
    /**
     * Cancels the authorization for the given order if supported.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    public function cancel(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        if (($tid = $this->get_order_service_item($order)->get_attribute('TRANSACTIONID', 'tx')) === null) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $order->get_id()));
        }
        $values = $this->get_auth_parameter();
        $values['METHOD'] = 'DoVoid';
        $values['AUTHORIZATIONID'] = $tid;
        $url_query = http_build_query($values, '', '&');
        $response = $this->send($this->apiendpoint, 'POST', $url_query);
        $this->check_response($order->get_id(), $response, __METHOD__);
        return $order->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_CANCELED);
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
        $params = $request->get_query_params();
        if (!isset($params['txn_id'])) {
            //tid from ipn
            return $response->with_status(400, 'PayPal Express: Parameter "txn_id" is missing');
        }
        $url_query = http_build_query($params, '', '&');
        //validation
        $result = $this->send($this->get_config_value(['paypalexpress.url-validate']), 'POST', $url_query);
        if ($result !== 'VERIFIED') {
            return $response->with_status(400, sprintf('PayPal Express: Invalid request "%1$s"', $url_query));
        }
        $manager = \Aimeos\M_Shop::create($this->context(), 'order');
        $order = $manager->get($params['invoice'], ['order/base', 'order/service']);
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        $service_item = $this->get_basket_service($order, $type, $this->get_service_item()->get_code());
        $this->check_ipn($order, $params);
        $status = ['PAYMENTSTATUS' => $params['payment_status']];
        if (isset($params['pending_reason'])) {
            $status['PENDINGREASON'] = $params['pending_reason'];
        }
        $service_item->add_attribute_items($this->attributes(['TRANSACTIONID' => $params['txn_id']], 'tx'))->add_attribute_items($this->attributes([$params['txn_id'] => $params['payment_status']], 'paypal/txn'));
        $manager->save($this->set_status_payment($order, $status));
        return $response->with_status(200);
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
        $params = (array) $request->get_attributes() + (array) $request->get_parsed_body() + (array) $request->get_query_params();
        if (!isset($params['token'])) {
            $msg = sprintf($this->context()->translate('mshop', 'Required parameter "%1$s" is missing'), 'token');
            throw new \Aimeos\M_Shop\Service\Exception($msg);
        }
        if (!isset($params['PayerID'])) {
            $msg = sprintf($this->context()->translate('mshop', 'Required parameter "%1$s" is missing'), 'PayerID');
            throw new \Aimeos\M_Shop\Service\Exception($msg);
        }
        $price = $order_item->get_price();
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        $service_item = $this->get_basket_service($order_item, $type, $this->get_service_item()->get_code());
        $values = $this->get_auth_parameter();
        $values['METHOD'] = 'DoExpressCheckoutPayment';
        $values['TOKEN'] = $params['token'];
        $values['PAYERID'] = $params['PayerID'];
        $values['PAYMENTACTION'] = $this->get_config_value(['paypalexpress.PaymentAction'], 'Sale');
        $values['CURRENCYCODE'] = $price->get_currency_id();
        $values['AMT'] = $this->get_amount($price);
        $url_query = http_build_query($values, '', '&');
        $response = $this->send($this->apiendpoint, 'POST', $url_query);
        $rvals = $this->check_response($order_item->get_id(), $response, __METHOD__);
        $attributes = ['PAYERID' => $params['PayerID']];
        if (isset($rvals['TRANSACTIONID'])) {
            $attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
            $attrs = [$rvals['TRANSACTIONID'] => $rvals['PAYMENTSTATUS']];
            $service_item->add_attribute_items($this->attributes($attrs, 'paypal/txn'));
        }
        $service_item->add_attribute_items($this->attributes($attributes, 'tx'));
        return $this->set_status_payment($order_item, $rvals);
    }
    /**
     * Checks what features the payment provider implements.
     *
     * @param int $what Constant from abstract class
     * @return bool True if feature is available in the payment provider, false if not
     */
    public function is_implemented(int $what): bool
    {
        return match ($what) {
            \Aimeos\M_Shop\Service\Provider\Payment\Base::FEAT_CAPTURE, \Aimeos\M_Shop\Service\Provider\Payment\Base::FEAT_QUERY, \Aimeos\M_Shop\Service\Provider\Payment\Base::FEAT_CANCEL, \Aimeos\M_Shop\Service\Provider\Payment\Base::FEAT_REFUND => true,
            default => false,
        };
    }
    /**
     * Checks the response from the payment server.
     *
     * @param string $orderid Order item ID
     * @param string $response Response from the payment provider
     * @param string $method Name of the calling method
     * @return array Associative list of key/value pairs containing the response parameters
     * @throws \Aimeos\MShop\Service\Exception If request was not successful and an error was returned
     */
    protected function check_response(string $orderid, string $response, string $method): array
    {
        $rvals = [];
        parse_str($response, $rvals);
        if ($rvals['ACK'] !== 'Success') {
            $safe_vals = array_diff_key($rvals, array_flip(['USER', 'PWD', 'SIGNATURE']));
            $msg = 'PayPal Express: method = ' . $method . ', order ID = ' . $orderid . ', response = ' . print_r($safe_vals, true);
            $this->context()->logger()->warning($msg, 'core/service/paypalexpress');
            if ($rvals['ACK'] !== 'SuccessWithWarning') {
                $short = $rvals['L_SHORTMESSAGE0'] ?? '<none>';
                $msg = $this->context()->translate('mshop', 'PayPal Express: Request for order ID "%1$s" failed with "%2$s"');
                throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $orderid, $short));
            }
        }
        return $rvals;
    }
    /**
     * Checks if IPN message from paypal is valid.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Order base item
     * @param array $params List of parameters
     * @return \Aimeos\MShop\Service\Provider\Payment\Iface Same object for fluent interface
     */
    protected function check_ipn(\Aimeos\M_Shop\Order\Item\Iface $basket, array $params): \Aimeos\M_Shop\Service\Provider\Payment\Iface
    {
        $attr_manager = \Aimeos\M_Shop::create($this->context(), 'order/service/attribute');
        if ($this->get_config_value(['paypalexpress.AccountEmail']) !== $params['receiver_email']) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Wrong receiver email "%1$s"');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $params['receiver_email']));
        }
        $price = $basket->get_price();
        $expected_currency = $price->get_currency_id();
        $actual_currency = $params['mc_currency'] ?? $params['currency_code'] ?? null;
        if ($actual_currency !== null && $actual_currency !== $expected_currency) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Wrong payment currency "%1$s" for order ID "%2$s"');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $actual_currency, $params['invoice']));
        }
        if ((float) $this->get_amount($price) !== (float) ($params['payment_amount'] ?? $params['mc_gross'] ?? 0)) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Wrong payment amount "%1$s" for order ID "%2$s"');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $params['payment_amount'] ?? $params['mc_gross'] ?? 0, $params['invoice']));
        }
        $search = $attr_manager->filter();
        $expr = [$search->compare('==', 'order.service.attribute.code', $params['txn_id']), $search->compare('==', 'order.service.attribute.value', $params['payment_status'])];
        $search->set_conditions($search->and($expr));
        if (!$attr_manager->search($search)->is_empty()) {
            $msg = $this->context()->translate('mshop', 'PayPal Express: Duplicate transaction with ID "%1$s" and status "%2$s"');
            throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, $params['txn_id'], $params['txn_status']));
        }
        return $this;
    }
    /**
     * Maps the PayPal status to the appropriate payment status and sets it in the order object.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $invoice Order invoice object
     * @param array $response Associative list of key/value pairs containing the PayPal response
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
     */
    protected function set_status_payment(\Aimeos\M_Shop\Order\Item\Iface $invoice, array $response): \Aimeos\M_Shop\Order\Item\Iface
    {
        if (!isset($response['PAYMENTSTATUS'])) {
            return $invoice;
        }
        switch ($response['PAYMENTSTATUS']) {
            case 'Pending':
                if (isset($response['PENDINGREASON'])) {
                    if ($response['PENDINGREASON'] === 'authorization') {
                        $invoice->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_AUTHORIZED);
                        break;
                    }
                    $str = 'PayPal Express: order ID = ' . $invoice->get_id() . ', PENDINGREASON = ' . $response['PENDINGREASON'];
                    $this->context()->logger()->info($str, 'core/service/paypalexpress');
                }
                $invoice->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_PENDING);
                break;
            case 'In-Progress':
                $invoice->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_PENDING);
                break;
            case 'Completed':
            case 'Processed':
                $invoice->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_RECEIVED);
                break;
            case 'Failed':
            case 'Denied':
            case 'Expired':
                $invoice->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_REFUSED);
                break;
            case 'Refunded':
            case 'Partially-Refunded':
            case 'Reversed':
                $invoice->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_REFUND);
                break;
            case 'Canceled-Reversal':
            case 'Voided':
                $invoice->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_CANCELED);
                break;
            default:
                $str = 'PayPal Express: order ID = ' . $invoice->get_id() . ', response = ' . print_r($response, true);
                $this->context()->logger()->info($str, 'core/service/paypalexpress');
        }
        return $invoice;
    }
    /**
     * Returns an list of order data required by PayPal.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $orderBase Order base item
     * @return array Associative list of key/value pairs with order data required by PayPal
     */
    protected function get_order_details(\Aimeos\M_Shop\Order\Item\Iface $order_base): array
    {
        $last_pos = 0;
        $delivery_prices = [];
        $values = $this->get_auth_parameter();
        $precision = $order_base->get_price()->get_precision();
        if ($this->get_config_value('paypalexpress.address', true)) {
            if (($addresses = $order_base->get_address(\Aimeos\M_Shop\Order\Item\Address\Base::TYPE_DELIVERY)) === []) {
                $addresses = $order_base->get_address(\Aimeos\M_Shop\Order\Item\Address\Base::TYPE_PAYMENT);
            }
            if ($address = current($addresses)) {
                /* setting up the address details */
                $values['NOSHIPPING'] = $this->get_config_value(['paypalexpress.NoShipping'], 1);
                $values['ADDROVERRIDE'] = $this->get_config_value(['paypalexpress.AddrOverride'], 0);
                $values['PAYMENTREQUEST_0_SHIPTONAME'] = $address->get_first_name() . ' ' . $address->get_last_name();
                $values['PAYMENTREQUEST_0_SHIPTOSTREET'] = $address->get_address1() . ' ' . $address->get_address2() . ' ' . $address->get_address3();
                $values['PAYMENTREQUEST_0_SHIPTOCITY'] = $address->get_city();
                $values['PAYMENTREQUEST_0_SHIPTOSTATE'] = $address->get_state();
                $values['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $address->get_country_id();
                $values['PAYMENTREQUEST_0_SHIPTOZIP'] = $address->get_postal();
            }
        }
        $item_delivery_costs = 0;
        if ($this->get_config_value('paypalexpress.product', true)) {
            foreach ($order_base->get_products() as $product) {
                $price = $product->get_price();
                $last_pos = $product->get_position();
                $delivery_price = clone $price;
                $delivery_prices = $this->add_price($delivery_prices, $delivery_price->set_value('0.00'), $product->get_quantity());
                $values['L_PAYMENTREQUEST_0_NUMBER' . $last_pos] = $product->get_id();
                $values['L_PAYMENTREQUEST_0_NAME' . $last_pos] = $product->get_name();
                $values['L_PAYMENTREQUEST_0_QTY' . $last_pos] = $product->get_quantity();
                $values['L_PAYMENTREQUEST_0_AMT' . $last_pos] = $this->get_amount($price, false);
            }
            foreach ($delivery_prices as $price_item) {
                $item_delivery_costs += $this->get_amount($price_item, true, true, $precision);
            }
        }
        if ($this->get_config_value('paypalexpress.service', true)) {
            foreach ($order_base->get_service('payment') as $service) {
                $price = $service->get_price();
                if (($payment_costs = $this->get_amount($price)) > '0.00') {
                    $last_pos++;
                    $values['L_PAYMENTREQUEST_0_NAME' . $last_pos] = $this->context()->translate('mshop', 'Payment costs');
                    $values['L_PAYMENTREQUEST_0_QTY' . $last_pos] = '1';
                    $values['L_PAYMENTREQUEST_0_AMT' . $last_pos] = $payment_costs;
                }
            }
            try {
                $last_pos = 0;
                foreach ($order_base->get_service('delivery') as $service) {
                    $delivery_prices = $this->add_price($delivery_prices, $service->get_price());
                    $values['L_SHIPPINGOPTIONAMOUNT' . $last_pos] = number_format($service->get_price()->get_costs() + $item_delivery_costs, $precision, '.', '');
                    $values['L_SHIPPINGOPTIONLABEL' . $last_pos] = $service->get_code();
                    $values['L_SHIPPINGOPTIONNAME' . $last_pos] = $service->get_name();
                    $values['L_SHIPPINGOPTIONISDEFAULT' . $last_pos] = 'true';
                    $last_pos++;
                }
            } catch (\Exception) {
            }
            // If no delivery service is available
        }
        $delivery_costs = 0;
        $price = $order_base->get_price();
        $amount = $this->get_amount($price);
        foreach ($delivery_prices as $price_item) {
            $delivery_costs += $this->get_amount($price_item, true, true, $precision);
        }
        $values['MAXAMT'] = $amount + 1 / 10 ** $precision;
        // possible rounding error
        $values['PAYMENTREQUEST_0_AMT'] = $amount;
        $values['PAYMENTREQUEST_0_ITEMAMT'] = number_format($amount - $delivery_costs, $precision, '.', '');
        $values['PAYMENTREQUEST_0_SHIPPINGAMT'] = number_format($delivery_costs, $precision, '.', '');
        $values['PAYMENTREQUEST_0_INSURANCEAMT'] = '0.00';
        $values['PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED'] = 'false';
        $values['PAYMENTREQUEST_0_SHIPDISCAMT'] = '0.00';
        $values['PAYMENTREQUEST_0_CURRENCYCODE'] = $order_base->get_price()->get_currency_id();
        $values['PAYMENTREQUEST_0_PAYMENTACTION'] = $this->get_config_value(['paypalexpress.PaymentAction'], 'sale');
        if ($localecode = $this->get_config_value('paypalexpress.LocaleCode')) {
            $values['LOCALECODE'] = $localecode;
        }
        return $values;
    }
    /**
     * Returns the data required for authorization against the PayPal server.
     *
     * @return array Associative list of key/value pairs containing the autorization parameters
     */
    protected function get_auth_parameter(): array
    {
        return ['VERSION' => '204.0', 'SIGNATURE' => $this->get_config_value(['paypalexpress.ApiSignature']), 'USER' => $this->get_config_value(['paypalexpress.ApiUsername']), 'PWD' => $this->get_config_value(['paypalexpress.ApiPassword'])];
    }
    /**
     * Returns order service item for specified base ID.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item
     */
    protected function get_order_service_item(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        return $this->get_basket_service($order, $type, $this->get_service_item()->get_code());
    }
    /**
     * Adds the costs to the price item with the corresponding tax rate
     *
     * @param \Aimeos\MShop\Price\Item\Iface[] $prices Associative list of tax rates as key and price items as value
     * @param \Aimeos\MShop\Price\Item\Iface $price Price item that should be added
     * @param int $quantity Product quantity
     * @return \Aimeos\MShop\Price\Item\Iface[] Updated list of price items
     */
    protected function add_price(array $prices, \Aimeos\M_Shop\Price\Item\Iface $price, int $quantity = 1): array
    {
        $taxrate = $price->get_tax_rate();
        if (!isset($prices[$taxrate])) {
            $prices[$taxrate] = \Aimeos\M_Shop::create($this->context(), 'price')->create();
            $prices[$taxrate]->set_tax_rate($taxrate);
        }
        $prices[$taxrate]->add_item($price, $quantity);
        return $prices;
    }
    /**
     * Sends request parameters to the providers interface.
     *
     * @param string $target Receivers address e.g. url.
     * @param string $method Initial method (e.g. post or get)
     * @param string $payload Update information whose format depends on the payment provider
     * @return string response body of a http request
     */
    public function send(string $target, string $method, string $payload): string
    {
        if (($curl = curl_init()) === false) {
            throw new \Aimeos\M_Shop\Service\Exception('Could not initialize curl');
        }
        try {
            curl_setopt($curl, CURLOPT_URL, $target);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 25);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // return data as string
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            if (($response = curl_exec($curl)) === false) {
                $msg = $this->context()->translate('mshop', 'Sending order failed: "%1$s"');
                throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, curl_error($curl)));
            }
            if (curl_errno($curl)) {
                $msg = $this->context()->translate('mshop', 'Curl error: "%1$s" - "%2$s"');
                throw new \Aimeos\M_Shop\Service\Exception(sprintf($msg, curl_errno($curl), curl_error($curl)));
            }
            curl_close($curl);
        } catch (\Exception $e) {
            curl_close($curl);
            throw $e;
        }
        return $response;
    }
}