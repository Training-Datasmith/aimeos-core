<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Provider\Catalog\Decorator;

/**
 * Category rule decorator.
 *
 * @package MShop
 * @subpackage Rule
 */
class Category extends \Aimeos\M_Shop\Rule\Provider\Catalog\Decorator\Base implements \Aimeos\M_Shop\Rule\Provider\Catalog\Decorator\Iface
{
    private array $codes;
    private array $be_config = ['category.code' => ['code' => 'category.code', 'internalcode' => 'category.code', 'label' => 'Category codes', 'default' => '', 'required' => true]];
    /**
     * Initializes the rule instance
     *
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
     * @param \Aimeos\MShop\Rule\Provider\Iface $provider Rule provider object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Rule\Item\Iface $item, \Aimeos\M_Shop\Rule\Provider\Iface $provider)
    {
        parent::__construct($context, $item, $provider);
        $this->codes = array_filter(explode(',', str_replace(' ', '', $this->get_config_value('category.code', ''))));
    }
    /**
     * Checks the backend configuration attributes for validity.
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid resp. null for attributes whose values are OK
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
        return array_merge(parent::get_config_be(), $this->get_config_items($this->be_config));
    }
    /**
     * Applies the rule to the given products
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
     * @return bool True if rule is the last one, false to continue with further rules
     */
    public function apply(\Aimeos\M_Shop\Product\Item\Iface $product): bool
    {
        foreach ($product->get_ref_items('catalog') as $cat_item) {
            if (in_array($cat_item->get_code(), $this->codes)) {
                return $this->get_provider()->apply($product);
            }
        }
        return false;
    }
}