<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager\Lists_Ref;

/**
 * Interface for all manager implementations using lists items
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
    /**
     * Creates a new lists item object
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Common\Item\Lists\Iface New lists item object
     */
    public function create_list_item(array $values = []): \Aimeos\M_Shop\Common\Item\Lists\Iface;
}