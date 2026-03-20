<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Rating;

/**
 * Generic interface for items with ratings
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Returns the rating of the item
     *
     * @return string Decimal value of the item rating
     */
    public function get_rating(): string;
    /**
     * Returns the total number of ratings for the item
     *
     * @return int Total number of ratings for the item
     */
    public function get_ratings(): int;
}