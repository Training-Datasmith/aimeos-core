<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Plugin
 */
namespace Aimeos\M_Shop\Plugin\Provider\Decorator;

/**
 * Logging and tracing for plugins.
 *
 * @package MShop
 * @subpackage Plugin
 */
class Log extends \Aimeos\M_Shop\Plugin\Provider\Decorator\Base implements \Aimeos\M_Shop\Plugin\Provider\Decorator\Iface
{
    /**
     * Subscribes itself to a publisher
     *
     * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
     */
    public function register(\Aimeos\M_Shop\Order\Item\Iface $p): \Aimeos\M_Shop\Plugin\Provider\Iface
    {
        $this->context()->logger()->debug('Plugin::register: ' . $this->get_provider()::class, 'core/plugin');
        $this->get_provider()->register($p);
        return $this;
    }
    /**
     * Receives a notification from a publisher object
     *
     * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
     * @param string $action Name of the action to listen for
     * @param mixed $value Object or value changed in publisher
     * @return mixed Modified value parameter
     */
    public function update(\Aimeos\M_Shop\Order\Item\Iface $order, string $action, $value = null)
    {
        $class = $this->get_provider()::class;
        $payload = is_object($value) ? $value::class : (is_scalar($value) ? $value : '');
        $msg = 'Plugin::update:before: ' . $class . ', action: ' . $action . ', value: ' . $payload;
        $this->context()->logger()->debug($msg, 'core/plugin');
        $value = $this->get_provider()->update($order, $action, $value);
        $msg = 'Plugin::update:after: ' . $class . ', action: ' . $action . ', value: ' . $payload;
        $this->context()->logger()->debug($msg, 'core/plugin');
        return $value;
    }
}