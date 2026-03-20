<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Coupon
 */
namespace Aimeos\M_Shop\Coupon\Provider;

/**
 * Percentage price coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Percent_Rebate extends \Aimeos\M_Shop\Coupon\Provider\Factory\Base implements \Aimeos\M_Shop\Coupon\Provider\Iface, \Aimeos\M_Shop\Coupon\Provider\Factory\Iface
{
    private array $be_config = ['percentrebate.productcode' => ['code' => 'percentrebate.productcode', 'internalcode' => 'percentrebate.productcode', 'label' => 'Product code of the rebate product', 'default' => '', 'required' => true], 'percentrebate.rebate' => ['code' => 'percentrebate.rebate', 'internalcode' => 'percentrebate.rebate', 'label' => 'Discount in percent', 'type' => 'number', 'default' => 0, 'required' => true], 'percentrebate.precision' => ['code' => 'percentrebate.precision', 'internalcode' => 'percentrebate.precision', 'label' => 'Number of decimal digits to round to', 'type' => 'int', 'default' => 2, 'required' => false], 'percentrebate.roundvalue' => ['code' => 'percentrebate.roundvalue', 'internalcode' => 'percentrebate.roundvalue', 'label' => 'Value to round rebate up/down', 'type' => 'number', 'default' => 0, 'required' => false]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        return $this->check_config($this->be_config, $attributes);
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return $this->get_config_items($this->be_config);
    }
    /**
     * Updates the result of a coupon to the order base instance.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
     * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Coupon\Provider\Iface
    {
        $percent = (float) $this->get_config_value('percentrebate.rebate', 0);
        $prodcode = $this->get_config_value('percentrebate.productcode');
        if ($percent == 0 || $prodcode === null) {
            $msg = $this->context()->translate('mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"');
            $msg = sprintf($msg, $this->get_item()->get_provider(), 'percentrebate.productcode, percentrebate.rebate');
            throw new \Aimeos\M_Shop\Coupon\Exception($msg);
        }
        $price = $this->object()->calc_price($order->set_coupon($this->get_code(), []));
        $rebate = $this->round(($price->get_value() + $price->get_costs() + $price->get_rebate()) * $percent / 100);
        $order->set_coupon($this->get_code(), $this->create_rebate_products($order, $prodcode, $rebate));
        return $this;
    }
    /**
     * Rounds the number to the configured precision
     *
     * @param float $number Number to round
     * @return float Rounded number
     */
    protected function round(float $number): float
    {
        $prec = $this->get_config_value('percentrebate.precision', 2);
        $value = $this->get_config_value('percentrebate.roundvalue', 0);
        if ($value == 0) {
            return round($number, $prec);
        }
        return round(round($number / $value) * $value, $prec);
    }
}