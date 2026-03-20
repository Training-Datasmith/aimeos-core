<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 * @package MShop
 * @subpackage Price
 */
namespace Aimeos\M_Shop\Price\Item;

/**
 * Basic methods for all price implementations
 *
 * @package MShop
 * @subpackage Price
 */
abstract class Base extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Price\Item\Iface
{
    /**
     * Compares the properties of the given price item with its own one.
     *
     * This method compare only the essential price properties:
     * * Value
     * * Costs
     * * Rebate
     * * Tax rate
     * * Tax flag
     * * Quantity
     * * Currency ID
     *
     * All other item properties are not compared.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $price Price item to compare with
     * @return bool True if equal, false if not
     * @since 2014.09
     */
    public function compare(\Aimeos\M_Shop\Price\Item\Iface $price): bool
    {
        if ($this->get_value() === $price->get_value() && $this->get_costs() === $price->get_costs() && $this->get_rebate() === $price->get_rebate() && $this->get_tax_flag() === $price->get_tax_flag() && $this->get_tax_rate() === $price->get_tax_rate() && $this->get_tax_rates() === $price->get_tax_rates() && $this->get_quantity() === $price->get_quantity() && $this->get_currency_id() === $price->get_currency_id()) {
            return true;
        }
        return false;
    }
    /**
     * Tests if the price is within the requirements.
     *
     * @param string|int|float|null $value Monetary value
     * @param int|null $precision Number of decimal digits, null for default value
     * @return string|null Sanitized monetary value
     */
    protected function check_price($value, ?int $precision = null): ?string
    {
        if ($value != '' && !is_numeric($value)) {
            throw new \Aimeos\M_Shop\Price\Exception(sprintf('Invalid characters in price "%1$s"', $value));
        }
        return $this->format_number($value !== '' ? $value : null, $precision);
    }
    /**
     * Formats the money value.
     *
     * @param string|int|float|null $number Money value
     * @param int|null $precision Number of decimal digits, null for default value
     * @return string|null Formatted money value
     */
    protected function format_number($number, ?int $precision = null): ?string
    {
        return $number !== null ? number_format($number, $precision ?: $this->get_precision(), '.', '') : null;
    }
}