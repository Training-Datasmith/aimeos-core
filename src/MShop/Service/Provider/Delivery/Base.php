<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Delivery;

/**
 * Abstract class for all delivery provider implementations.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base extends \Aimeos\M_Shop\Service\Provider\Base implements Iface
{
    /**
     * Feature constant if querying for status updates for an order is supported.
     */
    public const FEAT_QUERY = 1;
    /**
     * Sets the delivery attributes in the given service.
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem Order service item that will be added to the basket
     * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item with attributes added
     */
    public function set_config_fe(\Aimeos\M_Shop\Order\Item\Service\Iface $order_service_item, array $attributes): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $order_service_item->add_attribute_items($this->attributes($attributes, 'delivery'));
    }
}