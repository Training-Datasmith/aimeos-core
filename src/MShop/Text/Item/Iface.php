<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Text
 */
namespace Aimeos\M_Shop\Text\Item;

/**
 * Generic interface for text items created and saved by text managers.
 *
 * @package MShop
 * @subpackage Text
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Domain\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the ISO language code.
     *
     * @return string|null ISO language code (e.g. de or de_DE)
     */
    public function get_language_id(): ?string;
    /**
     * Sets the ISO language code.
     *
     * @param string|null $langid ISO language code (e.g. de or de_DE)
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function set_language_id(?string $langid): \Aimeos\M_Shop\Text\Item\Iface;
    /**
     * Returns the content of the text item.
     *
     * @return string Content of the text item
     */
    public function get_content(): string;
    /**
     * Sets the content of the text item.
     *
     * @param string $text Content of the text item
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function set_content(string $text): \Aimeos\M_Shop\Text\Item\Iface;
    /**
     * Returns the name of the attribute item.
     *
     * @return string Label of the attribute item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the attribute item.
     *
     * @param string $label Type label of the attribute item
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function set_label(?string $label): \Aimeos\M_Shop\Text\Item\Iface;
}