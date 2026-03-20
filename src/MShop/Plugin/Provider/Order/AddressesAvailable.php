<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Order;

/**
 * Checks if addresses are available in a basket as configured
 *
 * There are two address types by default:
 * - delivery
 * - payment
 *
 * For both types can be specifified if they are
 * - required (payment: 1 or delivery: 1)
 * - optional (payment: '' or delivery: '' or not set)
 * - not allowed (payment: 0 or delivery: 0)
 *
 * The checks are executed before the checkout summary page is rendered.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Addresses_Available extends \Aimeos\M_Shop\Plugin\Provider\Factory\Base implements \Aimeos\M_Shop\Plugin\Provider\Iface, \Aimeos\M_Shop\Plugin\Provider\Factory\Iface
{
    private array $be_config = ['payment' => ['code' => 'payment', 'internalcode' => 'payment', 'label' => 'Require billing address', 'type' => 'bool', 'default' => '', 'required' => false], 'delivery' => ['code' => 'delivery', 'internalcode' => 'delivery', 'label' => 'Require delivery address', 'type' => 'bool', 'default' => '', 'required' => false]];
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
     */
    public function check_config_be(array $attributes): array
    {
        $errors = parent::check_config_be($attributes);
        return array_merge($errors, $this->check_config($this->be_config, $attributes));
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
     * Subscribes itself to a publisher
     *
     * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
     * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $p): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $p->attach($this->object(), 'check.after');
        return $this;
    }
    /**
     * Receives a notification from a publisher object
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        if (!in_array('order/address', (array) $value)) {
            return $value;
        }
        $problems = [];
        $addresses = $order->get_addresses();
        foreach ($this->get_item_base()->get_config() as $type => $val) {
            if ($val == true && !isset($addresses[$type])) {
                $problems[$type] = 'available.none';
            }
            if ($val !== null && $val !== '' && $val == false && isset($addresses[$type])) {
                $problems[$type] = 'available.notallowed';
            }
        }
        if (count($problems) > 0) {
            $code = ['address' => $problems];
            $msg = $this->context()->translate('mshop', 'Checks for available addresses in basket failed');
            throw new \Aimeos\M_Shop\Plugin\Provider\Exception($msg, -1, null, $code);
        }
        return $value;
    }
}