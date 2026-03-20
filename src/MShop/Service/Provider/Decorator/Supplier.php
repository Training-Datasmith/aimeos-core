<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Decorator;

/**
 * Supplier address decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Supplier extends \Aimeos\M_Shop\Service\Provider\Decorator\Base implements \Aimeos\M_Shop\Service\Provider\Decorator\Iface
{
    private array $fe_config = ['supplier.code' => ['code' => 'supplier.code', 'internalcode' => 'supplier.code', 'label' => 'Pick-up address', 'type' => 'list', 'internaltype' => 'array', 'default' => [], 'required' => true]];
    /**
     * Initializes a new service provider object using the given context object.
     *
     * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider or decorator
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
     */
    public function __construct(\Aimeos\M_Shop\Service\Provider\Iface $provider, \Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Service\Item\Iface $service_item)
    {
        parent::__construct($provider, $context, $service_item);
        $manager = \Aimeos\M_Shop::create($context, 'supplier');
        $search = $manager->filter(true);
        $search->set_sortations([$search->sort('+', 'supplier.label')]);
        foreach ($manager->search($search, ['supplier/address']) as $item) {
            $addresses = $item->get_address_items();
            if (empty($addresses)) {
                $addresses[] = $manager->create_address_item();
            }
            foreach ($addresses as $id => $addr) {
                $addr_id = count($addresses) > 1 ? $item->get_code() . '-' . $id : $item->get_code();
                $this->fe_config['supplier.code']['default'][$addr_id] = trim(preg_replace("/\n+/m", "\n", sprintf(
                    /// Supplier address format with label (%1$s), company (%2$s),
                    /// address part one (%3$s, e.g street), address part two (%4$s, e.g house number), address part three (%5$s, e.g additional information),
                    /// postal/zip code (%6$s), city (%7$s), state (%8$s), country ID (%9$s),
                    /// e-mail (%10$s), phone (%11$s), facsimile/telefax (%12$s), web site (%13$s)
                    $context->translate('mshop', '%1$s
%2$s
%3$s %4$s
%5$s
%6$s %7$s
%8$s %9$s
%10$s
%11$s
%12$s
%13$s'),
                    $item->get_label(),
                    $addr->get_company(),
                    $addr->get_address1(),
                    $addr->get_address2(),
                    $addr->get_address3(),
                    $addr->get_postal(),
                    $addr->get_city(),
                    $addr->get_state(),
                    $context->translate('country', $addr->get_country_id()),
                    $addr->get_email(),
                    $addr->get_telephone(),
                    $addr->get_telefax(),
                    $addr->get_website()
                )));
                $this->fe_config['supplier.code']['short'][$addr_id] = trim(preg_replace("/\n+/m", "\n", sprintf(
                    /// Supplier address format with label (%1$s), company (%2$s),
                    /// address part one (%3$s, e.g street), address part two (%4$s, e.g house number), address part three (%5$s, e.g additional information),
                    /// postal/zip code (%6$s), city (%7$s), state (%8$s), country ID (%9$s),
                    /// e-mail (%10$s), phone (%11$s), facsimile/telefax (%12$s), web site (%13$s)
                    $context->translate('mshop', '%1$s, %2$s, %3$s %4$s, %6$s %7$s'),
                    $item->get_label(),
                    $addr->get_company(),
                    $addr->get_address1(),
                    $addr->get_address2(),
                    $addr->get_address3(),
                    $addr->get_postal(),
                    $addr->get_city(),
                    $addr->get_state(),
                    $context->translate('country', $addr->get_country_id()),
                    $addr->get_email(),
                    $addr->get_telephone(),
                    $addr->get_telefax(),
                    $addr->get_website()
                )));
            }
        }
    }
    /**
     * Checks the frontend configuration attributes for validity.
     *
     * @param array $attributes Attributes entered by the customer during the checkout process
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
     */
    public function check_config_fe(array $attributes): array
    {
        $result = $this->get_provider()->check_config_fe($attributes);
        return array_merge($result, $this->check_config($this->fe_config, $attributes));
    }
    /**
     * Returns the configuration attribute definitions of the provider to generate a list of available fields and
     * rules for the value of each field in the frontend.
     *
     * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
     * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
     */
    public function get_config_fe(\Aimeos\M_Shop\Order\Item\Iface $basket): array
    {
        $feconfig = $this->fe_config;
        try {
            $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY;
            $service = $this->get_basket_service($basket, $type, $this->get_service_item()->get_code());
            if (($value = $service->get_attribute('supplier.code', 'delivery')) != '' && isset($feconfig['supplier.code']['default'][$value])) {
                // move to first position so it's selected
                $address = $feconfig['supplier.code']['default'][$value];
                unset($feconfig['supplier.code']['default'][$value]);
                $feconfig['supplier.code']['default'] = [$value => $address] + $feconfig['supplier.code']['default'];
            }
        } catch (\Aimeos\M_Shop\Service\Exception) {
        }
        // If service isn't available
        return array_merge($this->get_provider()->get_config_fe($basket), $this->get_config_items($feconfig));
    }
    /**
     * Sets the delivery attributes in the given service.
     *
     * @param \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem Order service item that will be added to the basket
     * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item with attributes added
     */
    public function set_config_fe(\Aimeos\M_Shop\Order\Item\Service\Iface $order_service_item, array $attributes): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        if (($code = $attributes['supplier.code']) != '') {
            // add short address as attribute for summary page / customer email
            $attributes['supplier.address'] = $this->fe_config['supplier.code']['short'][$code];
            // remove code attribute for summary page / customer email
            $order_service_item->add_attribute_items($this->attributes(['supplier.code' => $attributes['supplier.code']], 'hidden'));
            unset($attributes['supplier.code']);
        }
        return $this->get_provider()->set_config_fe($order_service_item, $attributes);
    }
}