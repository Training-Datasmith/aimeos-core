<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Price
 */
namespace Aimeos\M_Shop\Price\Item;

/**
 * Generic interface for price DTO objects.
 *
 * @package MShop
 * @subpackage Price
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Domain\Iface, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface, \Aimeos\M_Shop\Common\Item\Property_Ref\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface, \Aimeos\M_Shop\Common\Item\Type_Ref\Iface
{
    /**
     * Add the given price to the current one.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $item Price item which should be added
     * @param float $quantity Number of times the Price should be added
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function add_item(\Aimeos\M_Shop\Price\Item\Iface $item, float $quantity = 1);
    /**
     * Resets the values of the price item.
     *
     * The currency ID, domain, type and status stays the same.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function clear();
    /**
     * Compares the properties of the given price item with its own one.
     *
     * This method compare only the essential price properties:
     * * Value
     * * Costs
     * * Rebate
     * * Taxrate
     * * Quantity
     * * Currency ID
     *
     * All other item properties are not compared.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $price Price item to compare with
     * @return boolean True if equal, false if not
     * @since 2014.09
     */
    public function compare(\Aimeos\M_Shop\Price\Item\Iface $price): bool;
    /**
     * Returns the decimal precision of the price
     *
     * @return int Number of decimal digits
     */
    public function get_precision(): int;
    /**
     * Returns the label of the item
     *
     * @return string Label of the item
     */
    public function get_label(): string;
    /**
     * Sets the label of the item
     *
     * @param string $label Label of the item
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_label(?string $label): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Returns the quantity.
     *
     * @return float Quantity
     */
    public function get_quantity(): float;
    /**
     * Sets the quantity.
     *
     * @param float $quantity Quantity
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_quantity(float $quantity): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Returns the amount of money.
     *
     * @return string|null Price value or NULL if price is on request
     */
    public function get_value(): ?string;
    /**
     * Sets the new amount of money.
     *
     * @param string|int|double|null $price Amount with configured precision or NULL if price is on request
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_value($price): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Returns the costs.
     *
     * @return string Costs
     */
    public function get_costs(): string;
    /**
     * Sets the new costs.
     *
     * @param string|int|double $price Amount with configured precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_costs($price): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Returns the rebate amount.
     *
     * @return string Rebate amount
     */
    public function get_rebate(): string;
    /**
     * Sets the new rebate amount.
     *
     * @param string|integer|double $price Rebate amount with two digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_rebate($price): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Returns the tax rate in percent.
     *
     * @return string Tax rate of product
     */
    public function get_tax_rate(): string;
    /**
     * Returns all tax rates in percent.
     *
     * @return string[] Tax rates for the price
     */
    public function get_tax_rates(): array;
    /**
     * Sets the new tax rate in percent.
     *
     * @param string|integer|double $taxrate Tax rate with two digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_rate($taxrate): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Sets the new tax rates in percent
     *
     * @param array $taxrates Tax rates with name as key and values with two digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_rates(array $taxrates);
    /**
     * Returns the tax rate flag.
     *
     * True if tax is included in the price value, costs and rebate, false if not
     *
     * @return bool Tax rate flag for the price
     */
    public function get_tax_flag(): bool;
    /**
     * Sets the new tax flag.
     *
     * @param bool $flag True if tax is included in the price value, costs and rebate, false if not
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_flag(bool $flag): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Returns the tax for the price item
     *
     * If the tax isn't set, it's calculated according to the value, the
     * costs per item, the tax rate and the tax flag.
     *
     * @return string Tax value with four digits precision
     * @see mshop/price/taxflag
     */
    public function get_tax_value(): string;
    /**
     * Sets the tax amount
     *
     * @param string|integer|double $value Tax value with up to four digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_value($value): \Aimeos\M_Shop\Price\Item\Iface;
    /**
     * Returns the currency ID.
     *
     * @return string|null Three letter ISO currency code (e.g. EUR)
     */
    public function get_currency_id(): ?string;
    /**
     * Sets the currency ID.
     *
     * @param string $currencyid Three letter ISO currency code (e.g. EUR)
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     * @throws \Aimeos\MShop\Exception If the currency ID is invalid
     */
    public function set_currency_id(string $currencyid): \Aimeos\M_Shop\Price\Item\Iface;
}