<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Manager;

/**
 * Abstract class for rule managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base extends \Aimeos\M_Shop\Common\Manager\Base
{
    private array $rules = [];
    /**
     * Applies the rules for modifying items dynamically
     *
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface $items Item or list of items
     * @param string $type Type of rules to apply to the items (e.g. "basket" or "catalog")
     * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Modified item or list of items
     */
    public function apply($items, string $type = 'catalog')
    {
        if (!isset($this->rules[$type])) {
            $this->rules[$type] = [];
            $manager = $this->object();
            $filter = $manager->filter(true)->add(['rule.type' => $type])->order('rule.position')->slice(0, 10000);
            foreach ($manager->search($filter) as $id => $rule_item) {
                $this->rules[$type][$id] = $manager->get_provider($rule_item, $type);
            }
        }
        foreach (map($items) as $item) {
            foreach ($this->rules[$type] as $rule) {
                // Selection products are handled by rule providers
                $article_ids = $item->get_type() === 'select' ? $item->get_ref_items('product', null, 'default')->keys() : [];
                $this->apply($item->get_ref_items('product')->except($article_ids), $type);
                if ($rule->apply($item)) {
                    break;
                }
            }
        }
        return $items;
    }
    /**
     * Returns the rule provider which is responsible for the rule item.
     *
     * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
     * @param string $type Rule type code
     * @return \Aimeos\MShop\Rule\Provider\Iface Returns the decoratad rule provider object
     * @throws \LogicException If provider couldn't be found
     */
    public function get_provider(\Aimeos\M_Shop\Rule\Item\Iface $item, string $type): \Aimeos\M_Shop\Rule\Provider\Iface
    {
        $type = ucwords($type);
        $context = $this->context();
        $names = explode(',', $item->get_provider());
        if (ctype_alnum($type) === false) {
            throw new \LogicException(sprintf('Invalid characters in type name "%1$s"', $type), 400);
        }
        if (($provider = array_shift($names)) === null) {
            throw new \LogicException(sprintf('Provider in "%1$s" not available', $item->get_provider()), 400);
        }
        if (ctype_alnum($provider) === false) {
            throw new \LogicException(sprintf('Invalid characters in provider name "%1$s"', $provider), 400);
        }
        $classname = '\Aimeos\MShop\Rule\Provider\\' . $type . '\\' . $provider;
        $interface = \Aimeos\M_Shop\Rule\Provider\Factory\Iface::class;
        $provider = \Aimeos\Utils::create($classname, [$context, $item], $interface);
        $provider = $this->add_rule_decorators($item, $provider, $names, $type);
        return $provider->set_object($provider);
    }
    /**
     *
     * @param \Aimeos\MShop\Rule\Item\Iface $ruleItem Rule item object
     * @param \Aimeos\MShop\Rule\Provider\Iface $provider Rule provider object
     * @param array $names List of decorator names that should be wrapped around the rule provider object
     * @param string $type Rule type code
     * @return \Aimeos\MShop\Rule\Provider\Iface Rule provider object
     */
    protected function add_rule_decorators(\Aimeos\M_Shop\Rule\Item\Iface $rule_item, \Aimeos\M_Shop\Rule\Provider\Iface $provider, array $names, string $type): \Aimeos\M_Shop\Rule\Provider\Iface
    {
        $context = $this->context();
        $classprefix = '\Aimeos\MShop\Rule\Provider\\' . $type . '\Decorator\\';
        foreach ($names as $name) {
            if (ctype_alnum($name) === false) {
                $msg = $this->context()->translate('mshop', 'Invalid characters in class name "%1$s"');
                throw new \Aimeos\M_Shop\Rule\Exception(sprintf($msg, $name), 400);
            }
            $classname = $classprefix . $name;
            $interface = $classprefix . 'Iface';
            $provider = \Aimeos\Utils::create($classname, [$context, $rule_item, $provider], $interface);
        }
        return $provider;
    }
}