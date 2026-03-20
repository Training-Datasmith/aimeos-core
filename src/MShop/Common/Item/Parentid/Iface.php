<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Parentid;

/**
 * Interface for items with parent/child relationship
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Returns the parent ID this item belongs to
     *
     * @return string Parent ID of the item
     */
    public function get_parent_id(): ?string;
    /**
     * Sets the new parent ID this item belongs to
     *
     * @param string|null $parentid New parent ID of the item
     * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
     */
    public function set_parent_id(?string $parentid): \Aimeos\M_Shop\Common\Item\Iface;
}