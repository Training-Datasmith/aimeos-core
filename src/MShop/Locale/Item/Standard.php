<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Locale
 */
namespace Aimeos\M_Shop\Locale\Item;

use Aimeos\M_Shop\Locale\Manager\Base as Locale;
/**
 * Common locale class containing the site, language and currency information.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Locale\Item\Iface
{
    /**
     * Initializes the object with the locale values.
     *
     * @param array $values Values to be set on initialisation
     * @param \Aimeos\MShop\Locale\Item\Site\Iface|null $siteItem Site item object
     * @param string[] $sitePath List of site IDs up to the root site item
     * @param string[]|string Site ID prefix or list of site IDs
     */
    public function __construct(array $values = [], private ?\Aimeos\M_Shop\Locale\Item\Site\Iface $site_item = null, private array $sites = [])
    {
        parent::__construct('locale.', $values);
    }
    /**
     * Clones internal objects of the locale item.
     */
    public function __clone()
    {
        $this->site_item = isset($this->site_item) ? clone $this->site_item : null;
    }
    /**
     * Returns the site code of the item.
     *
     * Caution: This will return the site code of the locale item, not the code
     * of the associated site item! If you need the site code of the site item,
     * you have to call getSiteItem() and getCode() on the returned object.
     *
     * @return string|null Site code or NULL if not available
     */
    public function get_site_code(): ?string
    {
        return $this->get('locale.sitecode');
    }
    /**
     * Returns the site item object.
     *
     * @return \Aimeos\MShop\Locale\Item\Site\Iface Site item object
     * @throws \Aimeos\MShop\Locale\Exception if site object isn't available
     */
    public function get_site_item(): \Aimeos\M_Shop\Locale\Item\Site\Iface
    {
        if ($this->site_item === null) {
            throw new \Aimeos\M_Shop\Locale\Exception('No site item available');
        }
        return $this->site_item;
    }
    /**
     * Returns the list site IDs up to the root site item.
     *
     * @return array List of site IDs
     */
    public function get_site_path(): array
    {
        return (array) ($this->sites[Locale::SITE_PATH] ?? $this->sites[Locale::SITE_ONE] ?? [$this->get('locale.siteid', '')]);
    }
    /**
     * Returns the site IDs for the locale site constants.
     *
     * @param int $level Site level constant from \Aimeos\MShop\Locale\Manager\Base
     * @return array|string Associative list of site constant as key and sites as values or site ID
     */
    public function get_sites(int $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL)
    {
        if ($level === Locale::SITE_ALL) {
            return $this->sites + [Locale::SITE_ONE => $this->get('locale.siteid', '')];
        }
        return $this->sites[$level] ?? $this->sites[Locale::SITE_ONE] ?? $this->get('locale.siteid', '');
    }
    /**
     * Returns the Site ID of the item.
     *
     * @return string Site ID (or null for global site)
     */
    public function get_site_id(): string
    {
        return $this->get('locale.siteid', '');
    }
    /**
     * Sets the identifier of the shop instance.
     *
     * @param string $id ID of the shop instance
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     */
    public function set_site_id(string $id): \Aimeos\M_Shop\Locale\Item\Iface
    {
        return $this->set('locale.siteid', $id);
    }
    /**
     * Returns the ISO language code.
     *
     * @return string|null ISO language code (e.g. de or de_DE)
     */
    public function get_language_id(): ?string
    {
        return $this->get('locale.languageid');
    }
    /**
     * Sets the ISO language code.
     *
     * @param string|null $id ISO language code (e.g. de or de_DE)
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     * @throws \Aimeos\MShop\Exception If the language ID is invalid
     */
    public function set_language_id(?string $id): \Aimeos\M_Shop\Locale\Item\Iface
    {
        return $this->set('locale.languageid', \Aimeos\Utils::language($id));
    }
    /**
     * Returns the currency ID.
     *
     * @return string|null Three letter ISO currency code (e.g. EUR)
     */
    public function get_currency_id(): ?string
    {
        return $this->get('locale.currencyid');
    }
    /**
     * Sets the currency ID.
     *
     * @param string|null $currencyid Three letter ISO currency code (e.g. EUR)
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     * @throws \Aimeos\MShop\Exception If the currency ID is invalid
     */
    public function set_currency_id(?string $currencyid): \Aimeos\M_Shop\Locale\Item\Iface
    {
        return $this->set('locale.currencyid', \Aimeos\Utils::currency($currencyid));
    }
    /**
     * Returns the position of the item.
     *
     * @return int Position of the item relative to the other items
     */
    public function get_position(): int
    {
        return (int) $this->get('locale.position', 0);
    }
    /**
     * Sets the position of the item.
     *
     * @param int $pos Position of the item
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     */
    public function set_position(int $pos): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('locale.position', $pos);
    }
    /**
     * Returns the status property of the locale item
     *
     * @return int Returns the status of the locale item
     */
    public function get_status(): int
    {
        return $this->get('locale.status', 1);
    }
    /**
     * Sets the status property
     *
     * @param int $status The status of the locale item
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('locale.status', $status);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_status() > 0;
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Locale\Item\Iface Locale item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'locale.siteid':
                    $item->set_site_id($value);
                    break;
                case 'locale.languageid':
                    $item->set_language_id($value);
                    break;
                case 'locale.currencyid':
                    $item->set_currency_id($value);
                    break;
                case 'locale.position':
                    $item->set_position((int) $value);
                    break;
                case 'locale.status':
                    $item->set_status((int) $value);
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
        $list['locale.currencyid'] = $this->get_currency_id();
        $list['locale.languageid'] = $this->get_language_id();
        $list['locale.position'] = $this->get_position();
        $list['locale.sitecode'] = $this->get_site_code();
        $list['locale.siteid'] = $this->get_site_id();
        $list['locale.status'] = $this->get_status();
        return $list;
    }
}