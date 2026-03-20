<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Delivery;

/**
 * Manual delivery provider implementation
 *
 * @package MShop
 * @subpackage Service
 */
class Standard extends \Aimeos\M_Shop\Service\Provider\Delivery\Base implements \Aimeos\M_Shop\Service\Provider\Delivery\Iface
{
    /**
     * Updates the delivery status
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Order instance
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item
     */
    public function push(iterable $orders): \Aimeos\Map
    {
        return map($orders)->set_status_delivery(\Aimeos\M_Shop\Order\Item\Base::STAT_PENDING);
    }
}