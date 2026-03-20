<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Delivery;

/**
 * Email delivery provider implementation
 *
 * @package MShop
 * @subpackage Service
 */
class Email extends \Aimeos\M_Shop\Service\Provider\Delivery\Base implements \Aimeos\M_Shop\Service\Provider\Delivery\Iface
{
    private array $be_config = ['email.from' => ['code' => 'email.from', 'internalcode' => 'email.from', 'label' => 'Sender e-mail address', 'default' => '', 'required' => false], 'email.to' => ['code' => 'email.to', 'internalcode' => 'email.to', 'label' => 'Recipient e-mail address', 'default' => '', 'required' => true], 'email.subject' => ['code' => 'email.subject', 'internalcode' => 'email.subject', 'label' => 'E-mail subject', 'default' => '', 'required' => false], 'email.template' => ['code' => 'email.template', 'internalcode' => 'email.template', 'label' => 'E-mail template', 'default' => 'service/provider/delivery/email-body', 'required' => true], 'email.order-template' => ['code' => 'email.order-template', 'internalcode' => 'email.order-template', 'label' => 'Order template', 'default' => 'service/provider/delivery/email-order', 'required' => true]];
    /**
     * Checks the backend configuration attributes for validity
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
     * Sends the email with several orders and updates the delivery status
     *
     * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
     * @return \Aimeos\MShop\Order\Item\Iface[] Updated order items
     */
    public function push(iterable $orders): \Aimeos\Map
    {
        return $this->send($orders)->set_status_delivery(\Aimeos\M_Shop\Order\Item\Base::STAT_PROGRESS);
    }
    /**
     * Returns the content for the e-mail body
     *
     * @param iterable $orderItems List of order items to export
     */
    protected function get_email_content(iterable $order_items)
    {
        $template = $this->get_config_value('email.template', 'service/provider/delivery/email-body');
        return $this->context()->view()->assign(['orderItems' => $order_items])->render($template);
    }
    /**
     * Returns the order content for the e-mail attachment
     *
     * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
     */
    protected function get_order_content(iterable $order_items)
    {
        $template = $this->get_config_value('email.order-template', 'service/provider/delivery/email-order');
        return $this->context()->view()->assign(['orderItems' => $order_items])->render($template);
    }
    /**
     * Sends an e-mail for the given orders
     *
     * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
     * @return \Aimeos\Map List of order items
     */
    protected function send(iterable $order_items): \Aimeos\Map
    {
        $this->context()->mail()->create()->to((string) $this->get_config_value('email.to'))->from((string) $this->get_config_value('email.from') ?: $this->context()->config()->get('resource/email/from-email'))->subject((string) $this->get_config_value('email.subject', 'New orders'))->attach($this->get_order_content($order_items), 'orders.csv', 'text/plain')->text($this->get_email_content($order_items))->send();
        return map($order_items);
    }
}