<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Stock
 */
namespace Aimeos\M_Shop\Stock\Item;

/**
 * Default product stock item implementation.
 *
 * @package MShop
 * @subpackage Stock
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Stock\Item\Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Returns the back in stock date of the
     *
     * @return string|null Back in stock date of the product
     */
    public function get_date_back(): ?string
    {
        return $this->get('stock.dateback');
    }
    /**
     * Sets the product back in stock date.
     *
     * @param string|null $dateback New back in stock date of the product
     * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
     */
    public function set_date_back(?string $dateback): \Aimeos\M_Shop\Stock\Item\Iface
    {
        return $this->set('stock.dateback', \Aimeos\Utils::datetime($dateback));
    }
    /**
     * Returns the ID of the product the stock item belongs to.
     *
     * @return string Product ID
     */
    public function get_product_id(): string
    {
        return $this->get('stock.productid', '');
    }
    /**
     * Sets a new product ID the stock item belongs to.
     *
     * @param string $value New product ID
     * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
     */
    public function set_product_id(string $value): \Aimeos\M_Shop\Stock\Item\Iface
    {
        return $this->set('stock.productid', $value);
    }
    /**
     * Returns the stock level.
     *
     * @return int|null Stock level
     */
    public function get_stock_level(): ?int
    {
        return $this->get('stock.stocklevel');
    }
    /**
     * Sets the stock level.
     *
     * @param int|null $stocklevel New stock level
     * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
     */
    public function set_stock_level($stocklevel = null): \Aimeos\M_Shop\Stock\Item\Iface
    {
        return $this->set('stock.stocklevel', is_numeric($stocklevel) ? (int) $stocklevel : null);
    }
    /**
     * Returns the expected delivery time frame
     *
     * @return string Expected delivery time frame
     */
    public function get_timeframe(): string
    {
        return $this->get('stock.timeframe', '');
    }
    /**
     * Sets the expected delivery time frame
     *
     * @param string $timeframe Expected delivery time frame
     * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
     */
    public function set_timeframe(?string $timeframe): \Aimeos\M_Shop\Stock\Item\Iface
    {
        return $this->set('stock.timeframe', (string) $timeframe);
    }
    /**
     * Returns the type of the stock item.
     * Overwritten for different default value.
     *
     * @return string Type of the stock item
     */
    public function get_type(): string
    {
        return $this->get('stock.type', 'default');
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'stock.productid':
                    $item->set_product_id($value);
                    break;
                case 'stock.stocklevel':
                    $item->set_stock_level($value);
                    break;
                case 'stock.timeframe':
                    $item->set_time_frame($value);
                    break;
                case 'stock.dateback':
                    $item->set_date_back($value);
                    break;
                case 'stock.type':
                    $item->set_type($value);
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
        $list['stock.productid'] = $this->get_product_id();
        $list['stock.stocklevel'] = $this->get_stock_level();
        $list['stock.timeframe'] = $this->get_time_frame();
        $list['stock.dateback'] = $this->get_date_back();
        $list['stock.type'] = $this->get_type();
        return $list;
    }
}