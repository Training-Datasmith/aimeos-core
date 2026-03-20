<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Item\Code;

/**
 * Generic interface for coupon codes created and saved by the coupon managers.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Time\Iface, \Aimeos\M_Shop\Common\Item\Parentid\Iface
{
    /**
     * Returns the code of the coupon item.
     *
     * @return string|null Coupon code
     */
    public function get_code(): ?string;
    /**
     * Sets the new code for the coupon item.
     *
     * @param string $code Coupon code
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Coupon\Item\Code\Iface;
    /**
     * Returns the number of tries the code is valid.
     *
     * @return int|null Number of available tries or null for unlimited
     */
    public function get_count(): ?int;
    /**
     * Sets the new number of tries the code is valid.
     *
     * @param int|null $count Number of tries or null for unlimited
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_count($count = null): \Aimeos\M_Shop\Coupon\Item\Code\Iface;
    /**
     * Returns reference for the coupon code
     * This can be an arbitrary value used by the coupon provider
     *
     * @return string Arbitrary value depending on the coupon provider
     */
    public function get_ref(): string;
    /**
     * Sets the new reference for the coupon code
     * This can be an arbitrary value used by the coupon provider
     *
     * @param string|null $ref Arbitrary value depending on the coupon provider
     * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
     */
    public function set_ref(?string $ref): \Aimeos\M_Shop\Coupon\Item\Code\Iface;
}