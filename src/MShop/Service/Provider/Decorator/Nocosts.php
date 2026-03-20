<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Decorator for service providers setting costs to zero.
 *
 * @package MShop
 * @subpackage Service
 */
class Nocosts extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    /**
     * Returns the costs per item as negative value to get no costs at all.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @param array $options Selected options by customer from frontend
     * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
     */
    public function calc_price(\Aimeos\M_Shop\Order\Item\Iface $basket, array $options = []): \Aimeos\M_Shop\Price\Item\Iface
    {
        $costs = 0;
        $price = $this->get_provider()->calc_price($basket, $options);
        foreach ($basket->get_products() as $product) {
            $costs += $product->get_price()->get_costs() * $product->get_quantity();
        }
        return $price->set_costs(-$costs);
    }
}