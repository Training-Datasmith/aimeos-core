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
 * Fixed price coupon model.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Fixed_Rebate extends \Aimeos\M_Shop\Coupon\Provider\Factory\Base implements \Aimeos\M_Shop\Coupon\Provider\Iface, \Aimeos\M_Shop\Coupon\Provider\Factory\Iface
{
    private array $be_config = ['fixedrebate.productcode' => ['code' => 'fixedrebate.productcode', 'internalcode' => 'fixedrebate.productcode', 'label' => 'Product code of the rebate product', 'default' => '', 'required' => true], 'fixedrebate.rebate' => ['code' => 'fixedrebate.rebate', 'internalcode' => 'fixedrebate.rebate', 'label' => 'Map of currency ID and rebate amount', 'type' => 'map', 'internaltype' => 'array', 'default' => [], 'required' => true]];
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
        $currency = $order->get_price()->get_currency_id();
        $rebate = $this->get_config_value('fixedrebate.rebate', []);
        $prodcode = $this->get_config_value('fixedrebate.productcode');
        if ($rebate == 0 || $prodcode === null || !is_array($rebate)) {
            $msg = $this->context()->translate('mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"');
            $msg = sprintf($msg, $this->get_item()->get_provider(), 'fixedrebate.productcode, fixedrebate.rebate');
            throw new \Aimeos\M_Shop\Coupon\Exception($msg);
        }
        if (isset($rebate[$currency])) {
            $price = $this->object()->calc_price($order);
            $sum = $price->get_value() + $price->get_costs() + $price->get_rebate();
            $rebate = $rebate[$currency] < $sum ? $rebate[$currency] : $sum;
            $order->set_coupon($this->get_code(), $this->create_rebate_products($order, $prodcode, $rebate));
        }
        return $this;
    }
}