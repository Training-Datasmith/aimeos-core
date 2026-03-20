<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */
namespace Aimeos\M_Shop\Order\Item\Service;

/**
 * Default order service item implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends Base implements Iface
{
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Clones internal objects of the order product item.
     */
    public function __clone()
    {
        $this->set('.transactions', map($this->get('.transactions', []))->clone());
        $this->set('.attributes', map($this->get('.attributes', []))->clone());
        $this->set('.price', clone $this->get('.price'));
        parent::__clone();
    }
    /**
     * Returns the price item for the service.
     *
     * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
     */
    public function get_price(): \Aimeos\M_Shop\Price\Item\Iface
    {
        return $this->get('.price');
    }
    /**
     * Sets the price item for the service.
     *
     * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_price(\Aimeos\M_Shop\Price\Item\Iface $price): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('.price', $price);
    }
    /**
     * Returns the associated service item
     *
     * @return \Aimeos\MShop\Service\Item\Iface|null Service item
     */
    public function get_service_item(): ?\Aimeos\M_Shop\Service\Item\Iface
    {
        return $this->get('.service');
    }
    /**
     * Returns the ID of the site the item is stored
     *
     * @return string Site ID (or null if not available)
     */
    public function get_site_id(): string
    {
        return $this->get('order.service.siteid', '');
    }
    /**
     * Sets the site ID of the item.
     *
     * @param string $value Unique site ID of the item
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_site_id(string $value): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('order.service.siteid', $value);
    }
    /**
     * Returns the order base ID of the order service if available.
     *
     * @return string|null Base ID of the item.
     */
    public function get_parent_id(): ?string
    {
        return $this->get('order.service.parentid');
    }
    /**
     * Sets the order service base ID of the order service item.
     *
     * @param string|null $value Order service base ID
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_parent_id(?string $value): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('order.service.parentid', $value);
    }
    /**
     * Returns the original ID of the service item used for the order.
     *
     * @return string Original service ID
     */
    public function get_service_id(): string
    {
        return $this->get('order.service.serviceid', '');
    }
    /**
     * Sets a new ID of the service item used for the order.
     *
     * @param string $servid ID of the service item used for the order
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_service_id(string $servid): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('order.service.serviceid', $servid);
    }
    /**
     * Returns the code of the service item.
     *
     * @return string Service item code
     */
    public function get_code(): string
    {
        return $this->get('order.service.code', '');
    }
    /**
     * Sets a new code for the service item.
     *
     * @param string $code Code as defined by the service provider
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('order.service.code', \Aimeos\Utils::code($code));
    }
    /**
     * Returns the name of the service item.
     *
     * @return string Service item name
     */
    public function get_name(): string
    {
        return $this->get('order.service.name', '');
    }
    /**
     * Sets a new name for the service item.
     *
     * @param string $name service item name
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_name(string $name): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('order.service.name', $name);
    }
    /**
     * Returns the location of the media.
     *
     * @return string Location of the media
     */
    public function get_media_url(): string
    {
        return $this->get('order.service.mediaurl', '');
    }
    /**
     * Sets the media url of the service item.
     *
     * @param string $value Location of the media/picture
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function set_media_url(string $value): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        return $this->set('order.service.mediaurl', $value);
    }
    /**
     * Returns the position of the service in the order.
     *
     * @return int|null Service position in the order from 0-n
     */
    public function get_position(): ?int
    {
        return $this->get('order.service.position');
    }
    /**
     * Sets the position of the service within the list of ordered servicees
     *
     * @param int|null $value Service position in the order from 0-n or null for resetting the position
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     * @throws \Aimeos\MShop\Order\Exception If the position is invalid
     */
    public function set_position(?int $value): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        if ($value < 0) {
            throw new \Aimeos\M_Shop\Order\Exception(sprintf('Order service position "%1$s" must be greater than 0', $value));
        }
        return $this->set('order.service.position', $value);
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $price = $this->get_price();
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'order.service.siteid':
                    !$private ?: $item->set_site_id($value);
                    break;
                case 'order.service.parentid':
                    !$private ?: $item->set_parent_id($value);
                    break;
                case 'order.service.serviceid':
                    !$private ?: $item->set_service_id($value);
                    break;
                case 'order.service.type':
                    $item->set_type($value);
                    break;
                case 'order.service.code':
                    $item->set_code($value);
                    break;
                case 'order.service.name':
                    $item->set_name($value);
                    break;
                case 'order.service.currencyid':
                    $price = $price->set_currency_id($value);
                    break;
                case 'order.service.price':
                    $price = $price->set_value($value);
                    break;
                case 'order.service.costs':
                    $price = $price->set_costs($value);
                    break;
                case 'order.service.rebate':
                    $price = $price->set_rebate($value);
                    break;
                case 'order.service.taxrates':
                    $price = $price->set_tax_rates($value);
                    break;
                case 'order.service.taxvalue':
                    $price = $price->set_tax_value($value);
                    break;
                case 'order.service.taxflag':
                    $price = $price->set_tax_flag($value);
                    break;
                case 'order.service.position':
                    $item->set_position($value);
                    break;
                case 'order.service.mediaurl':
                    $item->set_media_url($value);
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
     * @return array Associative list of item properties and their values.
     */
    public function to_array(bool $private = false): array
    {
        $price = $this->get_price();
        $list = parent::to_array($private);
        $list['order.service.type'] = $this->get_type();
        $list['order.service.code'] = $this->get_code();
        $list['order.service.name'] = $this->get_name();
        $list['order.service.currencyid'] = $price->get_currency_id();
        $list['order.service.price'] = $price->get_value();
        $list['order.service.costs'] = $price->get_costs();
        $list['order.service.rebate'] = $price->get_rebate();
        $list['order.service.taxrates'] = $price->get_tax_rates();
        $list['order.service.taxvalue'] = $price->get_tax_value();
        $list['order.service.taxflag'] = $price->get_tax_flag();
        $list['order.service.position'] = $this->get_position();
        $list['order.service.mediaurl'] = $this->get_media_url();
        $list['order.service.serviceid'] = $this->get_service_id();
        if ($private === true) {
            $list['order.service.parentid'] = $this->get_parent_id();
        }
        return $list;
    }
    /**
     * Copys all data from a given service item.
     *
     * @param \Aimeos\MShop\Service\Item\Iface $service New service item
     * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
     */
    public function copy_from(\Aimeos\M_Shop\Service\Item\Iface $service): \Aimeos\M_Shop\Order\Item\Service\Iface
    {
        if (self::macro('copyFrom')) {
            return $this->call('copyFrom', $service);
        }
        $values = $service->to_array();
        $this->from_array($values);
        $this->set_site_id($service->get_site_id());
        $this->set_code($service->get_code());
        $this->set_name($service->get_name());
        $this->set_type($service->get_type());
        $this->set_service_id($service->get_id());
        if (($item = $service->get_ref_items('media', 'default', 'default')->first()) !== null) {
            $this->set_media_url($item->get_url());
        }
        return $this->set_modified();
    }
}