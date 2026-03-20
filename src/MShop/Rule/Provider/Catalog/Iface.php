<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Provider\Catalog;

/**
 * Rule interface for dealing with run-time loadable extensions.
 *
 * @package MShop
 * @subpackage Rule
 */
interface Iface extends \Aimeos\M_Shop\Rule\Provider\Iface
{
    /**
     * Applies the rule to the given product
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
     * @return bool True if rule is the last one, false to continue with further rules
     */
    public function apply(\Aimeos\M_Shop\Product\Item\Iface $product): bool;
}