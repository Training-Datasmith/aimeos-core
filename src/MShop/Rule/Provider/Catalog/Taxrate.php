<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Provider\Catalog;

/**
 * Tax rate rule provider
 *
 * @package MShop
 * @subpackage Rule
 */
class Taxrate extends \Aimeos\M_Shop\Rule\Provider\Base implements \Aimeos\M_Shop\Rule\Provider\Catalog\Iface, \Aimeos\M_Shop\Rule\Provider\Factory\Iface
{
    private array $be_config = ['taxrate' => ['code' => 'taxrate', 'internalcode' => 'taxrate', 'label' => 'Product tax rate', 'type' => 'number', 'default' => '0.00', 'required' => true]];
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
        return array_replace(parent::get_config_be(), $this->get_config_items($this->be_config));
    }
    /**
     * Applies the rule to the given product
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
     * @return bool True if rule is the last one, false to continue with further rules
     */
    public function apply(\Aimeos\M_Shop\Product\Item\Iface $product): bool
    {
        $taxrate = $this->get_config_value('taxrate');
        foreach ($product->get_ref_items('product', null, 'default') as $subproduct) {
            $subproduct->get_ref_items('price')->set_taxrate($taxrate);
        }
        $product->get_ref_items('price')->set_taxrate($taxrate);
        return $this->is_last();
    }
}