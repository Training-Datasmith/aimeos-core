<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Item;

/**
 * Service item with common methods.
 *
 * @package MShop
 * @subpackage Service
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Service\Item\Iface
{
    use \Aimeos\M_Shop\Common\Item\Config\Traits;
    use \Aimeos\M_Shop\Common\Item\Lists_Ref\Traits;
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Initializes the item object.
     *
     * @param string $prefix Domain specific prefix string
     * @param array $values Parameter for initializing the basic properties
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values);
        $this->init_list_items($values['.listitems'] ?? []);
    }
    /**
     * Returns the code of the service item if available
     *
     * @return string Service item code
     */
    public function get_code(): string
    {
        return $this->get('service.code', '');
    }
    /**
     * Sets the code of the service item
     *
     * @param string $code Code of the service item
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_code(string $code): \Aimeos\M_Shop\Service\Item\Iface
    {
        return $this->set('service.code', \Aimeos\Utils::code($code));
    }
    /**
     * Returns the name of the service provider the item belongs to.
     *
     * @return string Name of the service provider
     */
    public function get_provider(): string
    {
        return $this->get('service.provider', '');
    }
    /**
     * Sets the new name of the service provider the item belongs to.
     *
     * @param string $provider Name of the service provider
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_provider(string $provider): \Aimeos\M_Shop\Service\Item\Iface
    {
        if (preg_match('/^[A-Za-z0-9]+(,[A-Za-z0-9]+)*$/', $provider) !== 1) {
            throw new \Aimeos\M_Shop\Service\Exception(sprintf('Invalid provider name "%1$s"', $provider));
        }
        return $this->set('service.provider', $provider);
    }
    /**
     * Returns the label of the service item if available.
     *
     * @return string Service item label
     */
    public function get_label(): string
    {
        return $this->get('service.label', '');
    }
    /**
     * Sets the label of the service item
     *
     * @param string $label Label of the service item
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_label(string $label): \Aimeos\M_Shop\Service\Item\Iface
    {
        return $this->set('service.label', $label);
    }
    /**
     * Returns the starting point of time, in which the service is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_start(): ?string
    {
        $value = $this->get('service.datestart');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new starting point of time, in which the service is available.
     *
     * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_date_start(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('service.datestart', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the ending point of time, in which the service is available.
     *
     * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
     */
    public function get_date_end(): ?string
    {
        $value = $this->get('service.dateend');
        return $value ? substr($value, 0, 19) : null;
    }
    /**
     * Sets a new ending point of time, in which the service is available.
     *
     * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
     * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
     */
    public function set_date_end(?string $date): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('service.dateend', \Aimeos\Utils::datetime($date));
    }
    /**
     * Returns the position of the service item in the list of deliveries.
     *
     * @return int Position in item list
     */
    public function get_position(): int
    {
        return $this->get('service.position', 0);
    }
    /**
     * Sets the new position of the service item in the list of deliveries.
     *
     * @param int $pos Position in item list
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_position(int $pos): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('service.position', $pos);
    }
    /**
     * Returns the status of the item.
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return $this->get('service.status', 1);
    }
    /**
     * Sets the status of the item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('service.status', $status);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        $date = $this->get('.date') ?: date('Y-m-d H:i:00');
        return parent::is_available() && $this->get_status() > 0 && ($this->get_date_start() === null || $this->get_date_start() < $date) && ($this->get_date_end() === null || $this->get_date_end() > $date);
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'service.type':
                    $item->set_type($value);
                    break;
                case 'service.code':
                    $item->set_code($value);
                    break;
                case 'service.label':
                    $item->set_label($value);
                    break;
                case 'service.provider':
                    $item->set_provider($value);
                    break;
                case 'service.datestart':
                    $item->set_date_start($value);
                    break;
                case 'service.dateend':
                    $item->set_date_end($value);
                    break;
                case 'service.status':
                    $item->set_status((int) $value);
                    break;
                case 'service.config':
                    $item->set_config((array) $value);
                    break;
                case 'service.position':
                    $item->set_position((int) $value);
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
        $list['service.type'] = $this->get_type();
        $list['service.code'] = $this->get_code();
        $list['service.label'] = $this->get_label();
        $list['service.provider'] = $this->get_provider();
        $list['service.position'] = $this->get_position();
        $list['service.datestart'] = $this->get_date_start();
        $list['service.dateend'] = $this->get_date_end();
        $list['service.config'] = $this->get_config();
        $list['service.status'] = $this->get_status();
        return $list;
    }
}