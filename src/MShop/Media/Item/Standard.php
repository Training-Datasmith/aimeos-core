<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Media
 */
namespace Aimeos\M_Shop\Media\Item;

use Aimeos\M_Shop\Common\Item\Lists_Ref;
use Aimeos\M_Shop\Common\Item\Property_Ref;
use Aimeos\M_Shop\Common\Item\Type_Ref;
/**
 * Default implementation of the media item.
 *
 * @package MShop
 * @subpackage Media
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Media\Item\Iface
{
    use Lists_Ref\Traits, Property_Ref\Traits, Type_Ref\Traits {
        Property_Ref\Traits::__clone as __cloneProperty;
        Lists_Ref\Traits::__clone as __cloneList;
        Lists_Ref\Traits::getName as getNameList;
    }
    private ?string $langid;
    /**
     * Initializes the media item object.
     *
     * @param string $prefix Domain specific prefix string
     * @param array $values Initial values of the media item
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values);
        $this->langid = $values['.languageid'] ?? null;
        $this->init_list_items($values['.listitems'] ?? []);
        $this->init_property_items($values['.propitems'] ?? []);
    }
    /**
     * Creates a deep clone of all objects
     */
    public function __clone()
    {
        parent::__clone();
        $this->__clone_list();
        $this->__clone_property();
    }
    /**
     * Returns the name of the file system the referenced file is stored.
     *
     * @return string Name of the file system
     */
    public function get_file_system(): string
    {
        return $this->get('media.filesystem', 'fs-media');
    }
    /**
     * Sets the name of the file system the referenced file is stored.
     *
     * @param string $value Name of the file system
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_file_system(string $value): \Aimeos\M_Shop\Media\Item\Iface
    {
        return $this->set('media.filesystem', $value);
    }
    /**
     * Returns the ISO language code.
     *
     * @return string|null ISO language code (e.g. de or de_DE)
     */
    public function get_language_id(): ?string
    {
        return $this->get('media.languageid');
    }
    /**
     * Sets the ISO language code.
     *
     * @param string|null $id ISO language code (e.g. de or de_DE)
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     * @throws \Aimeos\MShop\Exception If the language ID is invalid
     */
    public function set_language_id(?string $id): \Aimeos\M_Shop\Media\Item\Iface
    {
        return $this->set('media.languageid', \Aimeos\Utils::language($id));
    }
    /**
     * Returns the domain of the media item, if available.
     *
     * @return string Domain the media item belongs to
     */
    public function get_domain(): string
    {
        return (string) $this->get('media.domain', '');
    }
    /**
     * Sets the domain of the media item.
     *
     * @param string $domain Domain of media item
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_domain(string $domain): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('media.domain', $domain);
    }
    /**
     * Returns the label of the media item.
     *
     * @return string Label of the media item
     */
    public function get_label(): string
    {
        return (string) ($this->get('media.label') ?: basename($this->get_url()));
    }
    /**
     * Sets the new label of the media item.
     *
     * @param string $label Label of the media item
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_label(?string $label): \Aimeos\M_Shop\Media\Item\Iface
    {
        return $this->set('media.label', (string) $label);
    }
    /**
     * Returns the status of the media item.
     *
     * @return int Status of the item
     */
    public function get_status(): int
    {
        return (int) $this->get('media.status', 1);
    }
    /**
     * Sets the new status of the media item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('media.status', $status);
    }
    /**
     * Returns the mime type of the media item.
     *
     * @return string Mime type of the media item
     */
    public function get_mime_type(): string
    {
        return (string) $this->get('media.mimetype', '');
    }
    /**
     * Sets the new mime type of the media.
     *
     * @param string $mimetype Mime type of the media item
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_mime_type(string $mimetype): \Aimeos\M_Shop\Media\Item\Iface
    {
        if (preg_match('/^[a-z\-]+\/[a-zA-Z0-9\.\-\+]+$/', $mimetype) !== 1) {
            throw new \Aimeos\M_Shop\Media\Exception(sprintf('Invalid mime type "%1$s"', $mimetype));
        }
        return $this->set('media.mimetype', $mimetype);
    }
    /**
     * Returns the url of the media item.
     *
     * @param bool $version TRUE to add file version as parameter, FALSE for path only
     * @return string URL of the media file
     */
    public function get_url(bool $version = false): string
    {
        $url = (string) $this->get('media.url', '');
        if ($url && $version && !\Aimeos\Base\Str::starts($url, ['http', 'data:', '/']) && $this->get_time_modified()) {
            $url .= '?v=' . str_replace(['-', ' ', ':'], '', $this->get_time_modified());
        }
        return $url;
    }
    /**
     * Sets the new url of the media item.
     *
     * @param string|null $url URL of the media file
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_url(?string $url): \Aimeos\M_Shop\Media\Item\Iface
    {
        return $this->set('media.url', (string) $url);
    }
    /**
     * Returns the preview url of the media item.
     *
     * @param bool|int $size TRUE for the largest image, FALSE for the smallest or a concrete image width
     * @return string Preview URL of the media file
     */
    public function get_preview($width = false): string
    {
        if (($list = (array) $this->get('media.preview', [])) === []) {
            return $this->get_url();
        }
        ksort($list);
        $path = '';
        if ($width === false) {
            $path = reset($list);
        } elseif ($width === true) {
            $path = end($list);
        } elseif (isset($list[$width])) {
            $path = $list[$width];
        } else {
            $before = $after = [];
            foreach ($list as $idx => $path) {
                if ($idx < $width) {
                    $before[$idx] = $path;
                } else {
                    $after[$idx] = $path;
                }
            }
            if (($path = array_shift($after)) === null && ($path = array_pop($before)) === null) {
                return '';
            }
        }
        if ($path && !\Aimeos\Base\Str::starts($path, ['http', 'data:', '/']) && $this->get_time_modified()) {
            $path .= '?v=' . str_replace(['-', ' ', ':'], '', $this->get_time_modified());
        }
        return (string) $path;
    }
    /**
     * Returns all preview urls for images of different sizes.
     *
     * @param bool $version TRUE to add file version as parameter, FALSE for path only
     * @return array Associative list of widths in pixels as keys and urls as values
     */
    public function get_previews(bool $version = false): array
    {
        $previews = (array) $this->get('media.preview', []);
        if ($version && $this->get_time_modified()) {
            foreach ($previews as $key => $path) {
                if ($path && !\Aimeos\Base\Str::starts($path, ['http', 'data:', '/'])) {
                    $previews[$key] = $path . '?v=' . str_replace(['-', ' ', ':'], '', $this->get_time_modified());
                }
            }
        }
        return $previews;
    }
    /**
     * Sets the new preview url of the media item.
     *
     * @param string $url Preview URL of the media file
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_preview(string $url): \Aimeos\M_Shop\Media\Item\Iface
    {
        return $this->set('media.preview', [1 => $url]);
    }
    /**
     * Sets the new preview urls for images of different sizes.
     *
     * @param array $url List of preview URLs with widths of the media file in pixels as keys
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function set_previews(array $urls): \Aimeos\M_Shop\Media\Item\Iface
    {
        return $this->set('media.preview', $urls);
    }
    /**
     * Returns the localized text type of the item or the internal label if no name is available.
     *
     * @param string $type Text type to be returned
     * @param string|null $langId Two letter ISO Language code of the text
     * @return string Specified text type or label of the item
     */
    public function get_name(string $type = 'name', ?string $lang_id = null): string
    {
        foreach ($this->get_property_items($type) as $prop_item) {
            if ($prop_item->get_language_id() === $lang_id || $lang_id === null) {
                return $prop_item->get_value();
            }
        }
        return $this->get_name_list($type);
    }
    /**
     * Returns the type of the media item.
     * Overwritten for different default value.
     *
     * @return string Type of the media item
     */
    public function get_type(): string
    {
        return $this->get('media.type', 'default');
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        return parent::is_available() && $this->get_status() > 0 && ($this->langid === null || $this->get_language_id() === null || $this->get_language_id() === $this->langid);
    }
    /*
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'media.filesystem':
                    $item->set_file_system($value);
                    break;
                case 'media.domain':
                    $item->set_domain($value);
                    break;
                case 'media.label':
                    $item->set_label($value);
                    break;
                case 'media.languageid':
                    $item->set_language_id($value);
                    break;
                case 'media.mimetype':
                    $item->set_mime_type($value);
                    break;
                case 'media.type':
                    $item->set_type($value);
                    break;
                case 'media.url':
                    $item->set_url($value);
                    break;
                case 'media.preview':
                    $item->set_preview($value);
                    break;
                case 'media.previews':
                    $item->set_previews((array) $value);
                    break;
                case 'media.status':
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
        $list['media.filesystem'] = $this->get_file_system();
        $list['media.domain'] = $this->get_domain();
        $list['media.label'] = $this->get_label();
        $list['media.languageid'] = $this->get_language_id();
        $list['media.mimetype'] = $this->get_mime_type();
        $list['media.type'] = $this->get_type();
        $list['media.preview'] = $this->get_preview();
        $list['media.previews'] = $this->get_previews();
        $list['media.url'] = $this->get_url();
        $list['media.status'] = $this->get_status();
        return $list;
    }
}