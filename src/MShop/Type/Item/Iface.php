<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Type
 */
namespace Aimeos\M_Shop\Type\Item;

/**
 * Generic interface for type items
 *
 * @package MShop
 * @subpackage Type
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Domain\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the code of the type item
     *
     * @return string Code of the type item
     */
    public function get_code(): string;
    /**
     * Sets the code of the type item
     *
     * @param string $code New code of the type item
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Common\Item\Iface;
    /**
     * Returns the translated name for the type item
     *
     * @return string Translated name of the type item
     */
    public function get_name(): string;
    /**
     * Returns the label of the type item
     *
     * @return string Label of the type item
     */
    public function get_label(): string;
    /**
     * Sets the label of the type item
     *
     * @param string $label New label of the type item
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Type\Item\Iface;
    /**
     * Returns the translations of the type item label
     *
     * @return array Translations of the type item label
     */
    public function get_i18n(): array;
    /**
     * Sets the translations of the type item label
     *
     * @param array $value New translations of the type item label
     * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
     */
    public function set_i18n(array $value): \Aimeos\M_Shop\Type\Item\Iface;
}