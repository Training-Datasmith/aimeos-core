<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage PrTagoduct
 */
namespace Aimeos\M_Shop\Tag\Item;

/**
 * Default tag item implementation
 *
 * @package MShop
 * @subpackage Tag
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Domain\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the language id of the tag item
     *
     * @return string|null Language ID of the tag item
     */
    public function get_language_id(): ?string;
    /**
     * Sets the Language Id of the tag item
     *
     * @param string|null $id New Language ID of the tag item
     * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
     */
    public function set_language_id(?string $id): \Aimeos\M_Shop\Tag\Item\Iface;
    /**
     * Returns the label of the tag item.
     *
     * @return string Label of the tag item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the tag item.
     *
     * @param string $label Label of the tag item
     * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Tag\Item\Iface;
}