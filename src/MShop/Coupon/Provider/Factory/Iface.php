<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider\Factory;

/**
 * Factory interface for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
{
    /**
     * Initializes the coupon model.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item to set
     * @param string $code Coupon code entered by the customer
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Coupon\Item\Iface $item, string $code);
}