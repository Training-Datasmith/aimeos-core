<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Common
 */
namespace Aimeos\M_Shop\Common\Manager\Lists_Ref;

/**
 * Trait for managers working with referenced list items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
    /**
     * Returns the context object.
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    abstract protected function context(): \Aimeos\M_Shop\Context_Iface;
    /**
     * Returns the domain of the manager
     *
     * @return string Domain of the manager
     */
    abstract protected function domain(): string;
    /**
     * Returns the outmost decorator of the decorator stack
     *
     * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
     */
    abstract protected function object(): \Aimeos\M_Shop\Common\Manager\Iface;
    /**
     * Creates a new lists item object
     *
     * @param array $values Values the item should be initialized with
     * @return \Aimeos\MShop\Common\Item\Lists\Iface New list items object
     */
    public function create_list_item(array $values = []): \Aimeos\M_Shop\Common\Item\Lists\Iface
    {
        $domain = $this->domain();
        $context = $this->context();
        $values['.date'] = $context->datetime();
        $values[$domain . '.lists.siteid'] ??= $context->locale()->get_site_id();
        return new \Aimeos\M_Shop\Common\Item\Lists\Standard($domain . '.lists.', $values);
    }
    /**
     * Removes the items referenced by the given list items.
     *
     * @param \Aimeos\MShop\Common\Item\ListsRef\Iface[]|\Aimeos\Map|array $items List of items with deleted list items
     * @return \Aimeos\MShop\Common\Manager\ListsRef\Iface Manager object for method chaining
     */
    protected function delete_ref_items($items): \Aimeos\M_Shop\Common\Manager\Lists_Ref\Iface
    {
        if (($items = map($items))->is_empty()) {
            return $this;
        }
        $map = [];
        foreach ($items as $item) {
            if ($item instanceof \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface) {
                foreach ($item->get_list_items_deleted() as $list_item) {
                    if ($list_item->get_ref_item()) {
                        $map[$list_item->get_domain()][] = $list_item->get_ref_id();
                    }
                }
            }
        }
        foreach ($map as $domain => $ids) {
            \Aimeos\M_Shop::create($this->context(), $domain)->begin()->delete($ids)->commit();
        }
        return $this;
    }
    /**
     * Returns the list items that belong to the given parent item IDs.
     *
     * @param string[] $parentIds List of parent item IDs
     * @param string[] $ref List of domain names whose referenced items should be attached
     * @param string $domain Domain prefix
     * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface with IDs as keys
     */
    protected function get_list_items(array $parent_ids, array $ref, string $domain): array
    {
        if (empty($ref)) {
            return [];
        }
        $manager = $this->object()->get_sub_manager('lists');
        $search = $manager->filter()->slice(0, 0x7fffffff);
        $list = [];
        $len = strlen($domain);
        $expr = [$search->compare('==', $domain . '.lists.parentid', $parent_ids)];
        foreach ($ref as $key => $type) {
            if (is_array($type)) {
                $key = !strncmp($key, $domain . '/', $len + 1) ? [$key, substr($key, $len + 1)] : $key;
                // remove prefix
                $list[] = $search->and([$search->compare('==', $domain . '.lists.domain', $key), $search->compare('==', $domain . '.lists.type', $type)]);
            } else {
                $type = !strncmp($type, $domain . '/', $len + 1) ? [$type, substr($type, $len + 1)] : $type;
                // remove prefix
                $list[] = $search->compare('==', $domain . '.lists.domain', $type);
            }
        }
        if (!empty($list)) {
            $expr[] = $search->or($list);
        }
        return $manager->search($search->add($search->and($expr)), $ref)->uasort(fn($a, $b): int => $a->get_position() <=> $b->get_position())->all();
    }
    /**
     * Adds new, updates existing and deletes removed list items and referenced items if available
     *
     * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with referenced items
     * @param string $domain Domain of the calling manager
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item with updated referenced items
     */
    protected function save_list_items(\Aimeos\M_Shop\Common\Item\Lists_Ref\Iface $item, string $domain, bool $fetch = true): \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface
    {
        $context = $this->context();
        $rm_list_items = $rm_items = $ref_manager = [];
        $list_manager = $this->object()->get_sub_manager('lists');
        foreach ($item->get_list_items_deleted() as $list_item) {
            $rm_list_items[] = $list_item;
            if (($ref_item = $list_item->get_ref_item()) !== null) {
                $rm_items[$list_item->get_domain()][] = $ref_item->get_id();
            }
        }
        try {
            foreach ($rm_items as $ref_domain => $list) {
                $ref_manager[$ref_domain] = \Aimeos\M_Shop::create($context, $ref_domain);
                $ref_manager[$ref_domain]->begin();
                $ref_manager[$ref_domain]->delete($list);
            }
            $list_manager->delete($rm_list_items);
            foreach ($item->get_list_items(null, null, null, false) as $list_item) {
                $ref_domain = $list_item->get_domain();
                if (($ref_item = $list_item->get_ref_item()) !== null) {
                    if (!isset($ref_manager[$ref_domain])) {
                        $ref_manager[$ref_domain] = \Aimeos\M_Shop::create($context, $ref_domain);
                        $ref_manager[$ref_domain]->begin();
                    }
                    $ref_item = $ref_manager[$ref_domain]->save($ref_item);
                    $list_item->set_ref_id($ref_item->get_id());
                }
                if ($list_item->get_parent_id() && $list_item->get_parent_id() != $item->get_id()) {
                    $list_item->set_id(null);
                    // create new list item if copied
                }
                $list_manager->save($list_item->set_parent_id($item->get_id()), $fetch);
            }
            foreach ($ref_manager as $manager) {
                $manager->commit();
            }
        } catch (\Exception $e) {
            foreach ($ref_manager as $manager) {
                $manager->rollback();
            }
            throw $e;
        }
        return $item;
    }
}