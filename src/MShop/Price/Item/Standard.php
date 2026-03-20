<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Price
 */
namespace Aimeos\M_Shop\Price\Item;

use Aimeos\M_Shop\Common\Item\Lists_Ref;
use Aimeos\M_Shop\Common\Item\Property_Ref;
use Aimeos\M_Shop\Common\Item\Type_Ref;
/**
 * Default implementation of a price object.
 *
 * @package MShop
 * @subpackage Price
 */
class Standard extends Base
{
    use Lists_Ref\Traits, Property_Ref\Traits, Type_Ref\Traits {
        Lists_Ref\Traits::__clone as __cloneList;
        Property_Ref\Traits::__clone as __cloneProperty;
    }
    private int $precision;
    private ?string $tax;
    /**
     * Initalizes the object with the given values
     *
     * @param string $prefix Prefix for the keys returned by toArray()
     * @param array $values Associative array of key/value pairs for price, costs, rebate and currencyid
     */
    public function __construct(string $prefix, array $values = [])
    {
        $this->precision = (int) ($values['.precision'] ?? 2);
        $this->tax = $values['price.taxvalue'] ?? null;
        parent::__construct('price.', $values);
        $this->init_property_items($values['.propitems'] ?? []);
        $this->init_list_items($values['.listitems'] ?? []);
    }
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        $this->__clone_list();
        $this->__clone_property();
    }
    /**
     * Returns costs per item.
     *
     * @return string Costs per item
     */
    public function get_costs(): string
    {
        return $this->format_number((float) $this->get('price.costs', '0.00'));
    }
    /**
     * Sets the new costsper item.
     *
     * @param string|integer|double $price Amount with two digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_costs($price): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set('price.costs', $this->check_price((string) $price));
    }
    /**
     * Returns the currency ID.
     *
     * @return string|null Three letter ISO currency code (e.g. EUR)
     */
    public function get_currency_id(): ?string
    {
        return $this->get('price.currencyid');
    }
    /**
     * Sets the used currency ID.
     *
     * @param string $currencyid Three letter currency code
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     * @throws \Aimeos\MShop\Exception If the language ID is invalid
     */
    public function set_currency_id(string $currencyid): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set('price.currencyid', \Aimeos\Utils::currency($currencyid, false));
    }
    /**
     * Returns the domain the price is valid for.
     *
     * @return string Domain name
     */
    public function get_domain(): string
    {
        return $this->get('price.domain', '');
    }
    /**
     * Sets the new domain the price is valid for.
     *
     * @param string $domain Domain name
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_domain(string $domain): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('price.domain', $domain);
    }
    /**
     * Returns the label of the item
     *
     * @return string Label of the item
     */
    public function get_label(): string
    {
        return $this->get('price.label', '');
    }
    /**
     * Sets the label of the item
     *
     * @param string $label Label of the item
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_label(?string $label): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set('price.label', (string) $label);
    }
    /**
     * Returns the decimal precision of the price
     *
     * @return int Number of decimal digits
     */
    public function get_precision(): int
    {
        return $this->precision;
    }
    /**
     * Returns the quantity the price is valid for.
     *
     * @return float Quantity
     */
    public function get_quantity(): float
    {
        return (float) $this->get('price.quantity', 1);
    }
    /**
     * Sets the quantity the price is valid for.
     *
     * @param float $quantity Quantity
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_quantity(float $quantity): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set('price.quantity', $quantity);
    }
    /**
     * Returns the rebate amount.
     *
     * @return string Rebate amount
     */
    public function get_rebate(): string
    {
        return $this->format_number((float) $this->get('price.rebate', '0.00'));
    }
    /**
     * Sets the new rebate amount.
     *
     * @param string|integer|double $price Rebate amount with two digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_rebate($price): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set('price.rebate', $this->check_price((string) $price));
    }
    /**
     * Returns the status of the item
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('price.status', 1);
    }
    /**
     * Sets the status of the item
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('price.status', $status);
    }
    /**
     * Returns the tax rate
     *
     * @return string Tax rate
     */
    public function get_tax_rate(): string
    {
        $list = (array) $this->get('price.taxrates', []);
        return $this->format_number($list['tax'] ?? '0.00');
    }
    /**
     * Returns all tax rates in percent.
     *
     * @return string[] Tax rates for the price
     */
    public function get_tax_rates(): array
    {
        return $this->get('price.taxrates', []);
    }
    /**
     * Sets the new tax rate.
     *
     * @param string|integer|double $taxrate Tax rate with two digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_rate($taxrate): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set_tax_rates(['tax' => $taxrate]);
    }
    /**
     * Sets the new tax rates in percent
     *
     * @param array $taxrates Tax rates with name as key and values with two digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_rates(array $taxrates): \Aimeos\M_Shop\Price\Item\Iface
    {
        foreach ($taxrates as $name => $taxrate) {
            unset($taxrates[$name]);
            // change index 0 to ''
            $taxrates[$name ?: 'tax'] = $this->check_price($taxrate);
        }
        return $this->set('price.taxrates', $taxrates);
    }
    /**
     * Returns the tax rate flag.
     *
     * True if tax is included in the price value, costs and rebate, false if not
     *
     * @return bool Tax rate flag for the price
     */
    public function get_tax_flag(): bool
    {
        return $this->get('price.taxflag', true);
    }
    /**
     * Sets the new tax flag.
     *
     * @param bool $flag True if tax is included in the price value, costs and rebate, false if not
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_flag(bool $flag): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set('price.taxflag', $flag);
    }
    /**
     * Returns the tax for the price item
     *
     * @return string Tax value with four digits precision
     * @see mshop/price/taxflag
     */
    public function get_tax_value(): string
    {
        if ($this->tax === null) {
            $taxrate = array_sum($this->get_tax_rates());
            if ($this->get_tax_flag() !== false) {
                $this->tax = ($this->get_value() + $this->get_costs()) / (100 + $taxrate) * $taxrate;
            } else {
                $this->tax = ($this->get_value() + $this->get_costs()) * $taxrate / 100;
            }
            parent::set_modified();
        }
        return $this->format_number((float) $this->tax, $this->get_precision() + 2);
    }
    /**
     * Sets the tax amount
     *
     * @param string|integer|double $value Tax value with up to four digits precision
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_tax_value($value): \Aimeos\M_Shop\Price\Item\Iface
    {
        $this->tax = $this->check_price((string) $value, $this->get_precision() + 2);
        parent::set_modified();
        return $this;
    }
    /**
     * Returns the type of the price item.
     * Overwritten for different default value.
     *
     * @return string Type of the price item
     */
    public function get_type(): string
    {
        return $this->get('price.type', 'default');
    }
    /**
     * Returns the amount of money.
     *
     * @return string|null Price value or NULL for on request
     */
    public function get_value(): ?string
    {
        return $this->format_number($this->get('price.value'));
    }
    /**
     * Sets the new amount of money.
     *
     * @param string|integer|double|null $price Amount with configured precision or NULL for on request
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_value($price): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->set('price.value', $this->check_price($price));
    }
    /**
     * Sets the modified flag of the object.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function set_modified(): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->tax = null;
        return parent::set_modified();
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        $cid = $this->get('.currencyid');
        return parent::is_available() && $this->get_status() > 0 && ($cid === null || $this->get_currency_id() === $cid);
    }
    /**
     * Add the given price to the current one.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $item Price item which should be added
     * @param float $quantity Number of times the Price should be added
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function add_item(\Aimeos\M_Shop\Price\Item\Iface $item, float $quantity = 1): \Aimeos\M_Shop\Price\Item\Iface
    {
        if ($item->get_currency_id() != $this->get_currency_id()) {
            $msg = 'Price can not be added. Currency ID "%1$s" of price item and currently used currency ID "%2$s" does not match.';
            throw new \Aimeos\M_Shop\Price\Exception(sprintf($msg, $item->get_currency_id(), $this->get_currency_id()));
        }
        if ($this === $item) {
            $item = clone $item;
        }
        $tax_value = $this->get_tax_value();
        // use initial value before it gets reset
        $this->set_quantity(1);
        $this->set_value($this->get_value() + $item->get_value() * $quantity);
        $this->set_costs($this->get_costs() + $item->get_costs() * $quantity);
        $this->set_rebate($this->get_rebate() + $item->get_rebate() * $quantity);
        $this->set_tax_value($tax_value + $item->get_tax_value() * $quantity);
        return $this;
    }
    /**
     * Resets the values of the price item.
     * The currency ID, domain, type and status stays the same.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function clear(): \Aimeos\M_Shop\Common\Item\Iface
    {
        $this->set_quantity(1);
        $this->set_value('0.00');
        $this->set_costs('0.00');
        $this->set_rebate('0.00');
        $this->set_tax_rate('0.00');
        $this->tax = null;
        return $this->set_modified();
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'price.type':
                    $item->set_type($value);
                    break;
                case 'price.currencyid':
                    $item->set_currency_id($value);
                    break;
                case 'price.quantity':
                    $item->set_quantity((float) $value);
                    break;
                case 'price.domain':
                    $item->set_domain($value);
                    break;
                case 'price.value':
                    $item->set_value($value);
                    break;
                case 'price.costs':
                    $item->set_costs($value);
                    break;
                case 'price.rebate':
                    $item->set_rebate($value);
                    break;
                case 'price.taxvalue':
                    $item->set_tax_value($value);
                    break;
                case 'price.taxrate':
                    $item->set_tax_rate($value);
                    break;
                case 'price.taxrates':
                    $item->set_tax_rates((array) $value);
                    break;
                case 'price.taxflag':
                    $item->set_tax_flag((bool) $value);
                    break;
                case 'price.status':
                    $item->set_status((int) $value);
                    break;
                case 'price.label':
                    $item->set_label($value);
                    break;
                default:
                    continue 2;
            }
            unset($list[$key]);
        }
        return $item;
    }
    /**
     * Returns the item values as array.
     *
     * @param bool True to return private properties, false for public only
     * @return array Associative list of item properties and their values
     */
    public function to_array(bool $private = false): array
    {
        $list = parent::to_array($private);
        $list['price.type'] = $this->get_type();
        $list['price.currencyid'] = $this->get_currency_id();
        $list['price.domain'] = $this->get_domain();
        $list['price.quantity'] = $this->get_quantity();
        $list['price.value'] = $this->get_value();
        $list['price.costs'] = $this->get_costs();
        $list['price.rebate'] = $this->get_rebate();
        $list['price.taxvalue'] = $this->get_tax_value();
        $list['price.taxrates'] = $this->get_tax_rates();
        $list['price.taxrate'] = $this->get_tax_rate();
        $list['price.taxflag'] = $this->get_tax_flag();
        $list['price.status'] = $this->get_status();
        $list['price.label'] = $this->get_label();
        return $list;
    }
}