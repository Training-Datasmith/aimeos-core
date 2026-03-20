<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Provider\Factory;

/**
 * Rule factory interface for dealing with run-time loadable extensions.
 *
 * @package MShop
 * @subpackage Rule
 */
interface Iface
{
    /**
     * Initializes the rule object.
     *
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Rule\Item\Iface $item);
}