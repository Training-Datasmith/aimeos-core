<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MAdmin
 * @subpackage Job
 */
namespace Aimeos\M_Admin\Job\Item;

/**
 * MAdmin job item Interface.
 *
 * @package MAdmin
 * @subpackage Job
 */
interface Iface extends \Aimeos\M_Shop\Common\Item\Iface, \Aimeos\M_Shop\Common\Item\Status\Iface
{
    /**
     * Returns the name of the attribute item.
     *
     * @return string Label of the attribute item
     */
    public function get_label(): string;
    /**
     * Sets the new label of the attribute item.
     *
     * @param string|null $label Type label of the attribute item
     * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
     */
    public function set_label(?string $label): \Aimeos\M_Admin\Job\Item\Iface;
    /**
     * Returns the generated file path of the job.
     *
     * @return string Relative filesystem path to the generated file
     */
    public function get_path(): string;
    /**
     * Sets the new generated file path of the job.
     *
     * @param string|null $path Relative filesystem path to the generated file
     * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
     */
    public function set_path(?string $path): \Aimeos\M_Admin\Job\Item\Iface;
}