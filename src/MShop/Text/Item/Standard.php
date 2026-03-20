<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Text
 */
namespace Aimeos\M_Shop\Text\Item;

/**
 * Default text manager implementation.
 *
 * @package MShop
 * @subpackage Text
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Shop\Text\Item\Iface
{
    use \Aimeos\M_Shop\Common\Item\Lists_Ref\Traits;
    use \Aimeos\M_Shop\Common\Item\Type_Ref\Traits;
    /**
     * Initializes the text item object with the given values.
     *
     * @param string $prefix Domain specific prefix string
     * @param array $values Associative list of key/value pairs
     */
    public function __construct(string $prefix, array $values = [])
    {
        parent::__construct($prefix, $values);
        $this->init_list_items($values['.listitems'] ?? []);
    }
    /**
     * Returns the ISO language code.
     *
     * @return string|null ISO language code (e.g. de or de_DE)
     */
    public function get_language_id(): ?string
    {
        return $this->get('text.languageid');
    }
    /**
     * Sets the ISO language code.
     *
     * @param string|null $id ISO language code (e.g. de or de_DE)
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     * @throws \Aimeos\MShop\Exception If the language ID is invalid
     */
    public function set_language_id(?string $id): \Aimeos\M_Shop\Text\Item\Iface
    {
        return $this->set('text.languageid', \Aimeos\Utils::language($id));
    }
    /**
     * Returns the domain of the text item.
     *
     * @return string Domain of the text item
     */
    public function get_domain(): string
    {
        return $this->get('text.domain', '');
    }
    /**
     * Sets the domain of the text item.
     *
     * @param string $domain Domain of the text item
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function set_domain(string $domain): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('text.domain', $domain);
    }
    /**
     * Returns the content of the text item.
     *
     * @return string Content of the text item
     */
    public function get_content(): string
    {
        return $this->get('text.content', '');
    }
    /**
     * Sets the content of the text item.
     *
     * @param string $text Content of the text item
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function set_content(string $text): \Aimeos\M_Shop\Text\Item\Iface
    {
        ini_set('mbstring.substitute_character', 'none');
        return $this->set('text.content', @mb_convert_encoding($text, 'UTF-8', 'UTF-8'));
    }
    /**
     * Returns the name of the attribute item.
     *
     * @return string Label of the attribute item
     */
    public function get_label(): string
    {
        return $this->get('text.label', '');
    }
    /**
     * Sets the new label of the attribute item.
     *
     * @param string $label Type label of the attribute item
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function set_label(?string $label): \Aimeos\M_Shop\Text\Item\Iface
    {
        return $this->set('text.label', (string) $label);
    }
    /**
     * Returns the status of the text item.
     *
     * @return int Status of the text item
     */
    public function get_status(): int
    {
        return $this->get('text.status', 1);
    }
    /**
     * Sets the status of the text item.
     *
     * @param int $status true/false for enabled/disabled
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('text.status', $status);
    }
    /**
     * Tests if the item is available based on status, time, language and currency
     *
     * @return bool True if available, false if not
     */
    public function is_available(): bool
    {
        $langid = $this->get('.languageid');
        return parent::is_available() && $this->get_status() > 0 && ($langid === null || $this->get_language_id() === null || $langid === $this->get_language_id());
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'text.languageid':
                    $item->set_language_id($value);
                    break;
                case 'text.type':
                    $item->set_type($value);
                    break;
                case 'text.label':
                    $item->set_label($value);
                    break;
                case 'text.domain':
                    $item->set_domain($value);
                    break;
                case 'text.content':
                    $item->set_content($value);
                    break;
                case 'text.status':
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
        $list['text.languageid'] = $this->get_language_id();
        $list['text.type'] = $this->get_type();
        $list['text.label'] = $this->get_label();
        $list['text.domain'] = $this->get_domain();
        $list['text.content'] = $this->get_content();
        $list['text.status'] = $this->get_status();
        return $list;
    }
}