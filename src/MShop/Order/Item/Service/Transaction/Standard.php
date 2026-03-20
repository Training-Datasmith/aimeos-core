<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Service\Transaction;

/**
 * Default order item base service transaction.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Order\Item\Service\Transaction\Iface
{
    use \Aimeos\M_Shop\Common\Item\Config\Traits;
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Returns the ID of the site the item is stored
     *
     * @return string Site ID (or null if not available)
     */
    public function get_site_id(): string
    {
        return $this->get('order.service.transaction.siteid', '');
    }
    /**
     * Sets the site ID of the item.
     *
     * @param string $value Unique site ID of the item
     * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Service\Transaction\Iface
    {
        return $this->set('order.service.transaction.siteid', $value);
    }
    /**
     * Returns the ID of the ordered service item as parent
     *
     * @return string|null ID of the ordered service item
     */
    public function get_parent_id(): ?string
    {
        return $this->get('order.service.transaction.parentid');
    }
    /**
     * Sets the ID of the ordered service item as parent
     *
     * @param string|null $id ID of the ordered service item
     * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
     */
    public function set_parent_id(?string $id): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('order.service.transaction.parentid', $id);
    }
    /**
     * Returns the price item for the transaction.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
     */
    public function get_price(): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->get('.price');
    }
    /**
     * Sets the price item for the transaction.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
     * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
     */
    public function set_price(\Aimeos\M_Shop\Price\Item\Iface $price): \Aimeos\M_Shop\Order\Item\Service\Transaction\Iface
    {
        return $this->set('.price', $price);
    }
    /**
     * Returns the status of the transaction
     *
     * @return int Status of the transaction
     */
    public function get_status(): int
    {
        return $this->get('order.service.transaction.status', -1);
    }
    /**
     * Sets the new status of the transaction
     *
     * @param int $status New status of the transaction
     * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('order.service.transaction.status', $status);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order service transaction item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        $price = $item->get_price();
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.service.transaction.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'order.service.transaction.siteid':
                    !$private ?: $item->set_site_id($value);
                    break;
                case 'order.service.transaction.config':
                    $item->set_config((array) $value);
                    break;
                case 'order.service.transaction.status':
                    $item->set_status((int) $value);
                    break;
                case 'order.service.transaction.currencyid':
                    $price->set_currency_id($value);
                    break;
                case 'order.service.transaction.type':
                    $item->set_type($value);
                    break;
                case 'order.service.transaction.price':
                    $price->set_value($value);
                    break;
                case 'order.service.transaction.costs':
                    $price->set_costs($value);
                    break;
                case 'order.service.transaction.rebate':
                    $price->set_rebate($value);
                    break;
                case 'order.service.transaction.taxvalue':
                    $price->set_taxvalue($value);
                    break;
                case 'order.service.transaction.taxflag':
                    $price->set_taxflag((bool) $value);
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
        $price = $this->get_price();
        $list['order.service.transaction.type'] = $this->get_type();
        $list['order.service.transaction.config'] = $this->get_config();
        $list['order.service.transaction.status'] = $this->get_status();
        $list['order.service.transaction.currencyid'] = $price->get_currency_id();
        $list['order.service.transaction.price'] = $price->get_value();
        $list['order.service.transaction.costs'] = $price->get_costs();
        $list['order.service.transaction.rebate'] = $price->get_rebate();
        $list['order.service.transaction.taxvalue'] = $price->get_taxvalue();
        $list['order.service.transaction.taxflag'] = $price->get_taxflag();
        if ($private === true) {
            $list['order.service.transaction.parentid'] = $this->get_parent_id();
        }
        return $list;
    }
}