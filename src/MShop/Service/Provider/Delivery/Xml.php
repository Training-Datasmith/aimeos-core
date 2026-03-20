<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service\Provider\Delivery;

/**
 * XML delivery provider implementation
 *
 * @package MShop
 * @subpackage Service
 */
class Xml extends \Aimeos\M_Shop\Service\Provider\Delivery\Base implements \Aimeos\M_Shop\Service\Provider\Delivery\Iface
{
    private int $num = 0;
    private array $be_config = ['xml.backupdir' => ['code' => 'xml.backupdir', 'internalcode' => 'xml.backupdir', 'label' => 'Relative or absolute path of the backup directory (with date() placeholders)', 'default' => '', 'required' => false], 'xml.exportpath' => ['code' => 'xml.exportpath', 'internalcode' => 'xml.exportpath', 'label' => 'Relative or absolute path and name of the XML files (with date() placeholders)', 'default' => './order_%Y-%m-%d_%H:%i:%s_%v.xml', 'required' => true], 'xml.template' => ['code' => 'xml.template', 'internalcode' => 'xml.template', 'label' => 'Relative path of the template file name', 'default' => 'service/provider/delivery/xml-body', 'required' => false], 'xml.updatedir' => ['code' => 'xml.updatedir', 'internalcode' => 'xml.updatedir', 'label' => 'Relative or absolute path and name of the order update XML files', 'default' => '', 'required' => false]];
    /**
     * Checks the backend configuration attributes for validity
     *
     * @param array $attributes Attributes added by the shop owner in the administraton interface
     * @return array An array with the attribute keys as key and an error message as values for all attributes that are
     * 	known by the provider but aren't valid
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
        return $this->get_config_items($this->be_config);
    }
    /**
     * Sends the details of all orders to the ERP system for further processing
     *
     * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
     * @return \Aimeos\MShop\Order\Item\Iface[] Updated order items
     */
    public function push(iterable $orders): \Aimeos\Map
    {
        $this->create_file($this->create_xml($orders));
        foreach ($orders as $key => $order) {
            $orders[$key] = $order->set_status_delivery(\Aimeos\M_Shop\Order\Item\Base::STAT_PROGRESS);
        }
        return map($orders);
    }
    /**
     * Looks for new update files and updates the orders for which status updates were received.
     * If batch processing of files isn't supported, this method can be empty.
     *
     * @return bool True if the update was successful, false if async updates are not supported
     * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
     */
    public function update_async(): bool
    {
        $context = $this->context();
        $logger = $context->logger();
        $location = $this->require('xml.updatedir');
        if (!file_exists($location)) {
            $msg = sprintf('File or directory "%1$s" doesn\'t exist', $location);
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        $msg = sprintf('Started order status import from "%1$s"', $location);
        $logger->info($msg, 'core/service');
        $files = [];
        if (is_dir($location)) {
            foreach (new \Directory_Iterator($location) as $entry) {
                if (!strncmp($entry->get_filename(), 'order', 5) && $entry->get_extension() === 'xml') {
                    $files[] = $entry->get_pathname();
                }
            }
        } else {
            $files[] = $location;
        }
        sort($files);
        foreach ($files as $filepath) {
            $this->import_file($filepath);
        }
        $msg = sprintf('Finished order status import from "%1$s"', $location);
        $logger->info($msg, 'core/service');
        return true;
    }
    /**
     * Stores the content into the file
     *
     * @param string $content XML content
     * @return \Aimeos\MShop\Service\Provider\Delivery\Iface Same object for fluent interface
     */
    protected function create_file(string $content): \Aimeos\M_Shop\Service\Provider\Delivery\Iface
    {
        $filepath = $this->get_config_value('xml.exportpath', './order_%Y-%m-%d_%H:%i:%s_%v.xml');
        $filepath = sprintf(\Aimeos\Base\Str::strtime($filepath), $this->num++);
        if (file_put_contents($filepath, $content) === false) {
            $msg = sprintf('Unable to create order XML file "%1$s"', $filepath);
            throw new \Aimeos\M_Shop\Service\Exception($msg);
        }
        return $this;
    }
    /**
     * Creates the XML file for the given orders
     *
     * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
     * @return string Generated XML
     */
    protected function create_xml(iterable $order_items): string
    {
        $view = $this->context()->view();
        $template = $this->get_config_value('xml.template', 'service/provider/delivery/xml-body');
        return $view->assign(['orderItems' => $order_items])->render($template);
    }
    /**
     * Imports all orders from the given XML file name
     *
     * @param string $filename Relative or absolute path to the XML file
     * @return \Aimeos\MShop\Service\Provider\Delivery\Iface Same object for fluent interface
     */
    protected function import_file(string $filename): \Aimeos\M_Shop\Service\Provider\Delivery\Iface
    {
        $nodes = [];
        $xml = new \Xml_Reader();
        $logger = $this->context()->logger();
        if ($xml->open($filename, null, LIBXML_COMPACT | LIBXML_PARSEHUGE) === false) {
            $msg = $this->context()->translate('mshop', 'No XML file "%1$s" found');
            throw new \Aimeos\Controller\Jobs\Exception(sprintf($msg, $filename));
        }
        $msg = sprintf('Started order status import from file "%1$s"', $filename);
        $logger->info($msg, 'core/service');
        while ($xml->read() === true) {
            if ($xml->depth === 1 && $xml->node_type === \Xml_Reader::ELEMENT && $xml->name === 'orderitem') {
                if (($dom = $xml->expand()) === false) {
                    $msg = sprintf('Expanding "%1$s" node failed', 'orderitem');
                    throw new \Aimeos\Controller\Jobs\Exception($msg);
                }
                if (($attr = $dom->attributes->get_named_item('ref')) !== null) {
                    $nodes[$attr->node_value] = $dom;
                }
            }
        }
        $this->import_nodes($nodes);
        unset($nodes);
        $msg = sprintf('Finished order status import from file "%1$s"', $filename);
        $logger->info($msg, 'core/service');
        $backup = \Aimeos\Base\Str::strtime($this->get_config_value('xml.backupdir', ''));
        if (!empty($backup) && @rename($filename, $backup) === false) {
            $msg = sprintf('Unable to move imported file "%1$s" to "%2$s"', $filename, $backup);
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        return $this;
    }
    /**
     * Imports the orders from the given XML nodes
     *
     * @param \DomElement[] List of order DOM nodes
     * @return \Aimeos\MShop\Service\Provider\Delivery\Iface Same object for fluent interface
     */
    protected function import_nodes(array $nodes): \Aimeos\M_Shop\Service\Provider\Delivery\Iface
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'order');
        $search = $manager->filter()->slice(0, count($nodes));
        $search->set_conditions($search->compare('==', 'order.id', array_keys($nodes)));
        $items = $manager->search($search);
        foreach ($nodes as $node) {
            $list = [];
            foreach ($node->child_nodes as $child_node) {
                $list[$child_node->node_name] = $child_node->node_value;
            }
            if (($attr = $node->attributes->get_named_item('ref')) !== null && ($item = $items->get($attr->node_value)) !== null) {
                $item->from_array($list, true);
            }
        }
        $manager->save($items->to_array());
        return $this;
    }
}