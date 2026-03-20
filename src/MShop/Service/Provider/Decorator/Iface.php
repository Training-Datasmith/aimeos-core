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
 * Service decorator interface.
 *
 * @package MShop
 * @subpackage Service
 */
interface Iface extends \Aimeos\M_Shop\Service\Provider\Iface
{
    /**
     * Initializes a new service provider object using the given context object.
     *
     * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider or decorator
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
     */
    public function __construct(\Aimeos\M_Shop\Service\Provider\Iface $provider, \Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Service\Item\Iface $service_item);
}