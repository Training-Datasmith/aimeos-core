<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Item;

/**
 * Generic interface for delivery and payment item DTOs.
 * @package MShop
 * @subpackage Service
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Config\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Position\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Time\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Returns the code of the service item.
     *
     * @return string Service item code
     */
    public function get_code(): string;
    /**
     * Sets a new code for the service item.
     *
     * @param string $code Code as defined by the service provider
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Service\Item\Iface;
    /**
     * Returns the name of the service provider the item belongs to.
     *
     * @return string Name of the service provider
     */
    public function get_provider(): string;
    /**
     * Sets the new name of the service provider the item belongs to.
     *
     * @param string $provider Name of the service provider
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_provider(string $provider): \Aimeos\M_Shop\Service\Item\Iface;
    /**
     * Returns the label of the service item.
     *
     * @return string Service item label
     */
    public function get_label(): string;
    /**
     * Sets a new label for the service item.
     *
     * @param string $label Label as defined by the service provider
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Service\Item\Iface;
}