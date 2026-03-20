<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Status;

/**
 * Generic interface for items with status values
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Returns the status of the common list type item
     *
     * @return int Status of the common list type item
     */
    public function get_status(): int;
    /**
     * Sets the status of the common list type item
     *
     * @param int $status New status of the common list type item
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface;
}