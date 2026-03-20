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
 * Default job item implementation.
 *
 * @package MAdmin
 * @subpackage Job
 */
class Standard extends \Aimeos\M_Shop\Common\Item\Base implements \Aimeos\M_Admin\Job\Item\Iface
{
    /**
     * Initializes the job item.
     *
     * @param array $values Associative list of key/value pairs
     */
    public function __construct(array $values = [])
    {
        parent::__construct('job.', $values);
    }
    /**
     * Returns the name of the job item.
     *
     * @return string Label of the job item
     */
    public function get_label(): string
    {
        return $this->get('job.label', '');
    }
    /**
     * Sets the new label of the job item.
     *
     * @param string|null $label Type label of the job item
     * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
     */
    public function set_label(?string $label): \Aimeos\M_Admin\Job\Item\Iface
    {
        return $this->set('job.label', (string) $label);
    }
    /**
     * Returns the generated file path of the job.
     *
     * @return string Relative filesystem path to the generated file
     */
    public function get_path(): string
    {
        return $this->get('job.path', '');
    }
    /**
     * Sets the new generated file path of the job.
     *
     * @param string|null $path Relative filesystem path to the generated file
     * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
     */
    public function set_path(?string $path): \Aimeos\M_Admin\Job\Item\Iface
    {
        return $this->set('job.path', (string) $path);
    }
    /**
     * Returns the status (enabled/disabled) of the job item.
     *
     * @return int Returns the status of the item
     */
    public function get_status(): int
    {
        return $this->get('job.status', 1);
    }
    /**
     * Sets the new status of the job item.
     *
     * @param int $status Status of the item
     * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
     */
    public function set_status(int $status): \Aimeos\M_Shop\Common\Item\Iface
    {
        return $this->set('job.status', $status);
    }
    /**
     * Sets the item values from the given array and removes that entries from the list
     *
     * @param array &$list Associative list of item keys and their values
     * @param bool True to set private properties too, false for public only
     * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
     */
    public function from_array(array &$list, bool $private = false): \Aimeos\M_Shop\Common\Item\Iface
    {
        $item = parent::from_array($list, $private);
        foreach ($list as $key => $value) {
            switch ($key) {
                case 'job.path':
                    $item->set_path((string) $value);
                    break;
                case 'job.label':
                    $item->set_label((string) $value);
                    break;
                case 'job.status':
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
        $list['job.path'] = $this->get_path();
        $list['job.label'] = $this->get_label();
        $list['job.status'] = $this->get_status();
        return $list;
    }
}