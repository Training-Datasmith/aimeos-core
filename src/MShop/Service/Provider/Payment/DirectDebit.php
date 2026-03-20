<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Payment;

/**
 * Payment provider for direct debit orders.
 *
 * @package MShop
 * @subpackage Service
 */
class Direct_Debit extends \Aimeos\M_Shop\Service\Provider\Payment\Base implements \Aimeos\M_Shop\Service\Provider\Payment\Iface
{
    private array $fe_config = ['directdebit.accountowner' => ['code' => 'directdebit.accountowner', 'internalcode' => 'accountowner', 'label' => 'Account owner', 'default' => '', 'required' => true], 'directdebit.accountno' => ['code' => 'directdebit.accountno', 'internalcode' => 'accountno', 'label' => 'Account number', 'default' => '', 'required' => true], 'directdebit.bankcode' => ['code' => 'directdebit.bankcode', 'internalcode' => 'bankcode', 'label' => 'Bank code', 'default' => '', 'required' => true], 'directdebit.bankname' => ['code' => 'directdebit.bankname', 'internalcode' => 'bankname', 'label' => 'Bank name', 'default' => '', 'required' => true]];
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the frontend.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_fe(\Aimeos\M_Shop\Order\Item\Iface $basket): array
    {
        $feconfig = $this->fe_config;
        try {
            $address = $basket->get_address(\Aimeos\M_Shop\Order\Item\Address\Base::TYPE_PAYMENT, 0);
            if (($fn = $address->get_firstname()) !== '' && ($ln = $address->get_lastname()) !== '') {
                $feconfig['directdebit.accountowner']['default'] = $fn . ' ' . $ln;
            }
        } catch (\Aimeos\M_Shop\Order\Exception) {
        }
        // If address isn't available
        return $this->get_config_items($feconfig);
    }
    /**
     * Checks the frontend configuration attributes for validity.
     *
     * @param array $attributes Attributes entered by the customer during the checkout process
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
     */
    public function check_config_fe(array $attributes): array
    {
        return $this->check_config($this->fe_config, $attributes);
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
        $order_service_item->add_attribute_items($this->attributes($attributes));
        if (($attr_item = $order_service_item->get_attribute_item('directdebit.accountno')) !== null) {
            $attr_list = [$attr_item->get_code() => $attr_item->get_value()];
            $order_service_item->add_attribute_items($this->attributes($attr_list, 'hidden'));
            if (is_string($value = $attr_item->get_value())) {
                $len = strlen($value);
                $xstr = $len > 3 ? str_repeat('X', $len - 3) : '';
                $attr_item->set_value($xstr . substr($value, -3));
                $order_service_item->set_attribute_item($attr_item);
            }
        }
        return $order_service_item;
    }
    /**
     * Executes the payment again for the given order if supported.
     * This requires support of the payment gateway and token based payment
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item
     */
    public function repay(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        return $order->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_AUTHORIZED);
    }
    /**
     * Updates the orders for whose status updates have been received by the confirmation page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object with parameters and request body
     * @param \Aimeos\MShop\Order\Item\Iface $order Order item that should be updated
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item
     * @throws \Aimeos\MShop\Service\Exception If updating the orders failed
     */
    public function update_sync(\Psr\Http\Message\Server_Request_Interface $request, \Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        if ($order->get_status_payment() < 0) {
            $order->set_status_payment(\Aimeos\M_Shop\Order\Item\Base::PAY_AUTHORIZED);
        }
        return $order;
    }
}