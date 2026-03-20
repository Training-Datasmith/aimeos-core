<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager\Factory;

/**
 * Generic interface for all manager created by factories.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Initializes the manager by using the given context object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context);
}