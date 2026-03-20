<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Decorator for service providers checking the orders of a customer
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Order_Check extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $be_config = ['ordercheck.total-number-min' => ['code' => 'ordercheck.total-number-min', 'internalcode' => 'ordercheck.total-number-min', 'label' => 'Required minimum successful orders', 'type' => 'int', 'default' => 0, 'required' => true], 'ordercheck.limit-days-pending' => ['code' => 'ordercheck.limit-days-pending', 'internalcode' => 'ordercheck.limit-days-pending', 'label' => 'Number of days which must not contain pending orders', 'type' => 'int', 'default' => 0, 'required' => false]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        $error = $this->get_provider()->check_config_be($attributes);
        return $error + $this->check_config($this->be_config, $attributes);
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the administration interface.
     *
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_be(): array
    {
        return array_replace(parent::get_config_be(), $this->get_config_items($this->be_config));
    }
    /**
     * Checks if payment provider can be used based on the basket content.
     * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return bool True if payment provider can be used, false if not
     */
    public function is_available(\Aimeos\M_Shop\Order\Item\Iface $basket): bool
    {
        $context = $this->context();
        $config = $this->get_service_item()->get_config();
        if (($customer_id = $context->user()) === null) {
            return false;
        }
        $manager = \Aimeos\M_Shop::create($context, 'order');
        if (isset($config['ordercheck.total-number-min'])) {
            $search = $manager->filter(true);
            $expr = [$search->compare('==', 'order.customerid', $customer_id), $search->compare('>=', 'order.statuspayment', \Aimeos\M_Shop\Order\Item\Base::PAY_AUTHORIZED), $search->get_conditions()];
            $search->set_conditions($search->and($expr));
            $search->slice(0, $config['ordercheck.total-number-min']);
            if ($manager->search($search)->count() < (int) $config['ordercheck.total-number-min']) {
                return false;
            }
        }
        if (isset($config['ordercheck.limit-days-pending'])) {
            $time = time() - (int) $config['ordercheck.limit-days-pending'] * 86400;
            $search = $manager->filter(true);
            $expr = [$search->compare('==', 'order.customerid', $customer_id), $search->compare('>=', 'order.datepayment', date('Y-m-d H:i:s', $time)), $search->compare('==', 'order.statuspayment', \Aimeos\M_Shop\Order\Item\Base::PAY_PENDING), $search->get_conditions()];
            $search->set_conditions($search->and($expr));
            $search->slice(0, 1);
            if (!$manager->search($search)->is_empty()) {
                return false;
            }
        }
        return $this->get_provider()->is_available($basket);
    }
}