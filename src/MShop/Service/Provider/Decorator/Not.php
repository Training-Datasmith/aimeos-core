<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Negation decorator for service providers
 *
 * This decorator inverts the results of the following decorators or the
 * service provider itself. In combination with the category decorator that
 * enforces a product of a configured category being in the basket, it can be
 * use to disable the service option if such a product is in the basket.
 *
 * @package MShop
 * @subpackage Service
 */
class Not extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    /**
     * Checks if the products are withing the allowed code is allowed for the service provider.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        return !$this->get_provider()->is_available($basket);
    }
}