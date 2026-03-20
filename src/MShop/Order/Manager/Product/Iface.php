<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Manager\Product;

/**
 * Generic interface for order base product managers.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\M_Shop\Common\Manager\Iface
{
    /**
     * Creates a new order product attribute item instance
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface New order product attribute item object
     */
    public function create_attribute_item(array $values = []): \Aimeos\M_Shop\Common\Item\Iface;
}