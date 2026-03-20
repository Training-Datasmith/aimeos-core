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
 * Abstract class for all price managers with basic methods.
 *
 * @package MShop
 * @subpackage Price
 */
abstract class Base extends \Aimeos\M_Shop\Common\Manager\Base
{
    /**
     * Returns the price item with the lowest price for the given quantity.
     *
     * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
     * @param float $quantity Number of products
     * @param string|null $currencyId Three letter ISO currency code or null for all
     * @param string|null $siteId Site ID of the prices which should be used
     * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
     * @throws \Aimeos\MShop\Price\Exception if no price item is available
     */
    public function get_lowest_price(\Aimeos\Map $price_items, float $quantity, ?string $currency_id = null, ?string $site_id = null): \Aimeos\M_Shop\Price\Item\Iface
    {
        $price_list = $this->get_price_list($price_items, $currency_id, $site_id);
        if (($price = $price_list->first()) === null) {
            $msg = $this->context()->translate('mshop', 'Price item not available');
            throw new \Aimeos\M_Shop\Price\Exception($msg);
        }
        if ($price->get_quantity() > $quantity) {
            $msg = $this->context()->translate('mshop', 'Price for the given quantity "%1$s" not available');
            throw new \Aimeos\M_Shop\Price\Exception(sprintf($msg, $quantity));
        }
        return $this->call('calcLowestPrice', $price_list, $quantity);
    }
    /**
     * Returns the lowest price for the given quantity
     *
     * @param \Aimeos\Map $priceList Associative list of quantity as keys and price item as value
     * @param float $quantity Number of products
     * @return \Aimeos\MShop\Price\Item\Iface Price item with the lowest price
     */
    protected function calc_lowest_price(\Aimeos\Map $price_list, float $quantity): \Aimeos\M_Shop\Price\Item\Iface
    {
        $price = $price_list->first();
        foreach ($price_list as $qty => $price_item) {
            // add $priceItem->getValue() < $price->getValue() to use lowest price regardless of quantity
            if ($quantity >= $qty && $price->get_quantity() < $qty) {
                $price = $price_item;
            }
        }
        return $price;
    }
    /**
     * Returns the price items sorted by quantity
     *
     * @param \Aimeos\Map $priceItems List of price items implementing \Aimeos\MShop\Price\Item\Iface
     * @param string|null $currencyId Three letter ISO currency code or null for all
     * @param string|null $siteId Site ID of the prices which should be used
     * @return \Aimeos\Map Associative list of quantity as keys and price item as value
     * @throws \Aimeos\MShop\Price\Exception If an object is no price item
     */
    protected function get_price_list(\Aimeos\Map $price_items, ?string $currency_id, ?string $site_id): \Aimeos\Map
    {
        $list = map();
        $site_ids = $this->context()->locale()->get_site_path();
        $price_items->implements(\Aimeos\M_Shop\Price\Item\Iface::class, true);
        foreach ($price_items as $price_item) {
            if ($currency_id && $currency_id !== $price_item->get_currency_id()) {
                continue;
            }
            if ($site_id) {
                if (in_array($site_id, $site_ids)) {
                    // if product is inherited, inherit price too
                    if (!in_array($price_item->get_site_id(), $site_ids)) {
                        continue;
                    }
                } elseif ($price_item->get_site_id() !== $site_id) {
                    // Use price from specific site originally passed as parameter
                    continue;
                }
            }
            $qty = (string) $price_item->get_quantity();
            if (!isset($list[$qty]) || $list[$qty]->get_value() === null || $price_item->get_value() !== null && $list[$qty]->get_value() > $price_item->get_value()) {
                $list[$qty] = $price_item;
            }
        }
        return $list->ksort();
    }
}