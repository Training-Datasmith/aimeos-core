<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Price
 */
namespace Aimeos\M_Shop\Price\Manager;

/**
 * Generic price manager interface for creating and handling prices.
 * @package MShop
 * @subpackage Price
 */
interface Iface extends \Aimeos\M_Shop\Common\Manager\Iface
{
    /**
     * Returns the price item with the lowest price for the given quantity.
     *
     * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
     * @param float $quantity Number of products
     * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
     * @throws \Aimeos\MShop\Price\Exception if no price item is available
     */
    public function get_lowest_price(\Aimeos\Map $price_items, float $quantity): \Aimeos\M_Shop\Price\Item\Iface;
}