<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Rule
 */
namespace Aimeos\M_Shop\Rule\Manager;

/**
 * Rule manager interface
 * @package MShop
 * @subpackage Rule
 */
interface Iface extends \Aimeos\M_Shop\Common\Manager\Iface
{
    /**
     * Returns the rule provider which is responsible for the rule item
     *
     * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
     * @param string $type Rule type code
     * @return \Aimeos\MShop\Rule\Provider\Iface Returns the decoratad rule provider object
     * @throws \Aimeos\MShop\Rule\Exception If provider couldn't be found
     */
    public function get_provider(\Aimeos\M_Shop\Rule\Item\Iface $item, string $type): \Aimeos\M_Shop\Rule\Provider\Iface;
}