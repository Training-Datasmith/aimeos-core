<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MW
 * @subpackage Tree
 */
namespace Aimeos\MW\Tree\Node;

/**
 * Default implementation of a basic tree node
 *
 * @package MW
 * @subpackage Tree
 */
class Standard implements \Aimeos\MW\Tree\Node\Iface, \Countable
{
    private array $children;
    private bool $modified = false;
    /**
     * Initializes the instance with the given values.
     *
     * @param array $values Node values for internal use
     * @param \Aimeos\MW\Tree\Node\Iface[] $children Children of the node
     * @throws \RuntimeException if the children doesn't implement the interface
     */
    public function __construct(private array $values = [], array $children = [])
    {
        map($children)->implements(\Aimeos\MW\Tree\Node\Iface::class, true);
        $this->children = $children;
    }
    /**
     * Returns the value associated with the given name.
     *
     * @param string $name Name of member variable tried to access
     * @return mixed Value associated to the given name or NULL if not available
     */
    public function __get(string $name)
    {
        return $this->values[$name] ?? null;
    }
    /**
     * Sets the new value associated with the given name.
     *
     * @param string $name Name of member variable tried to access
     * @param mixed $value Value of member variable tried to access
     */
    public function __set(string $name, $value)
    {
        if (!array_key_exists($name, $this->values) || $this->values[$name] !== $value) {
            $this->values[$name] = $value;
            $this->modified = true;
        }
    }
    /**
     * Tests if a value for the given name is available.
     *
     * @param string $name Name of member variable tried to access
     * @return bool True if a value is available, false if not
     */
    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->values);
    }
    /**
     * Removes the value associated to the given name.
     *
     * @param string $name Name of member variable tried to access
     */
    public function __unset(string $name)
    {
        if (array_key_exists($name, $this->values)) {
            unset($this->values[$name]);
            $this->modified = true;
        }
    }
    /**
     * Returns the unique ID of the node.
     *
     * @return string|null Unique ID of th node
     */
    public function get_id(): ?string
    {
        return isset($this->values['id']) ? (string) $this->values['id'] : null;
    }
    /**
     * Sets the unique ID of the node.
     *
     * @param mixed|null $id Unique ID of the node
     * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
     */
    public function set_id(?string $id): Iface
    {
        $this->values['id'] = $id;
        $this->modified = $id === null ? true : false;
        return $this;
    }
    /**
     * Returns the name of the node.
     *
     * @return string Default name of the node
     */
    public function get_label(): string
    {
        return (string) ($this->values['label'] ?? '');
    }
    /**
     * Sets the new name of the node.
     *
     * @param string $name New default name of the node
     * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
     */
    public function set_label(string $name): Iface
    {
        if ($name !== $this->get_label()) {
            $this->values['label'] = $name;
            $this->modified = true;
        }
        return $this;
    }
    /**
     * Returns the Code of the node.
     *
     * @return string Code of the node
     */
    public function get_code(): string
    {
        return (string) ($this->values['code'] ?? '');
    }
    /**
     * Sets the new code of the node.
     *
     * @param string $name New code of the node
     * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
     */
    public function set_code(string $name): Iface
    {
        if ($name !== $this->get_code()) {
            $this->values['code'] = $name;
            $this->modified = true;
        }
        return $this;
    }
    /**
     * Returns the status of the node.
     *
     * @return int Greater than zero if enabled, zero or less than if not
     */
    public function get_status(): int
    {
        return (int) ($this->values['status'] ?? 1);
    }
    /**
     * Sets the new status of the node.
     *
     * @param int $status Greater than zero if enabled, zero or less than if not
     * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
     */
    public function set_status(int $status): Iface
    {
        if ($status !== $this->get_status()) {
            $this->values['status'] = $status;
            $this->modified = true;
        }
        return $this;
    }
    /**
     * Returns a child of this node identified by its index.
     *
     * @param int $index Index of child node
     * @return \Aimeos\MW\Tree\Node\Iface Selected node
     * @throws \Aimeos\MW\Tree\Exception If there's no child at the given position
     */
    public function get_child(int $index): Iface
    {
        if (isset($this->children[$index])) {
            return $this->children[$index];
        }
        throw new \Aimeos\MW\Tree\Exception('Invalid index for child');
    }
    /**
     * Returns all children of this node.
     *
     * @return \Aimeos\MW\Tree\Node\Iface[] Numerically indexed list of nodes
     */
    public function get_children(): array
    {
        return $this->children;
    }
    /**
     * Tests if a node has children.
     *
     * @return bool True if node has children, false if not
     */
    public function has_children(): bool
    {
        return count($this->children) > 0 ? true : false;
    }
    /**
     * Adds a child node to this node.
     *
     * @param \Aimeos\MW\Tree\Node\Iface $node Child node to add
     * @return \Aimeos\MW\Tree\Node\Iface Item object for method chaining
     */
    public function add_child(\Aimeos\MW\Tree\Node\Iface $node): Iface
    {
        // don't set the modified flag as it's only for the values
        $this->children[] = $node;
        return $this;
    }
    /**
     * Returns the public values of the node as array.
     *
     * @return array Assciative list of key/value pairs
     */
    public function to_array(): array
    {
        return ['id' => $this->get_id(), 'code' => $this->get_code(), 'label' => $this->get_label(), 'status' => $this->get_status()];
    }
    /**
     * Checks, whether this node was modified.
     *
     * @return bool True if the content of the node is modified, false if not
     */
    public function is_modified(): bool
    {
        return $this->modified;
    }
    /**
     * Counts children
     *
     * @return int Count of this nodes children
     */
    public function count(): int
    {
        return count($this->children);
    }
}