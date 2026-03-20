<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Item\Lists;

/**
 * Generic interface for all list items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Time\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface, \Aimeos\M_Shop\Common\Item\Parentid\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Domain\Iface
{
    /**
     * Returns the unique key of the list item
     *
     * @return string Unique key consisting of domain/type/refid
     */
    public function get_key(): string;
    /**
     * Returns the reference id of the common list item, like the unique id of a text item or a media item.
     *
     * @return string reference id of the common list item
     */
    public function get_ref_id(): string;
    /**
     * Sets the new reference id of the common list item, like the unique id of a text item or a media item.
     *
     * @param string $refid New reference id of the common list item
     * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
     */
    public function set_ref_id(string $refid): \Aimeos\M_Shop\Common\Item\Lists\Iface;
    /**
     * Returns the referenced item if it's available.
     *
     * @return \Aimeos\MShop\Common\Item\Iface|null Referenced list item
     */
    public function get_ref_item(): ?\Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Stores the item referenced by the list item.
     *
     * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Item referenced by the list item or null for no reference
     * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
     */
    public function set_ref_item(?\Aimeos\M_Shop\Common\Item\Iface $ref_item): \Aimeos\M_Shop\Common\Item\Lists\Iface;
}