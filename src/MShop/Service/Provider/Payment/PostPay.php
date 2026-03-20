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
 * Payment provider for post-paid orders.
 *
 * @package MShop
 * @subpackage Service
 */
class Post_Pay extends \Aimeos\M_Shop\Service\Provider\Payment\Base implements \Aimeos\M_Shop\Service\Provider\Payment\Iface
{
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