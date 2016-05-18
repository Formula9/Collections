<?php namespace Nine\Collections;

use Nine\Exceptions\ImmutableViolationException;
use Nine\Traits\WithImmutability;

/**
 * **A simple immutable attribute collection. **
 *
 * Set once and never again.
 *
 * Attributes may be used anywhere Scope (or context) is useful. The class
 * Adds context-appropriate access methods to the array of featured
 * provided by Scope.
 *
 * @package Nine Collections
 * @version 0.4.2
 * @author  Greg Truesdell
 */
class Attributes implements \ArrayAccess
{
    use WithImmutability;

    protected $items;

    /**
     * @param array|null $attributes
     */
    public function __construct($attributes = NULL)
    {
        // do not overwrite an uninitialized item array
        if ($attributes) {
            $this->items = $this->getArrayableItems($attributes);

            return;
        }
    }

    /**
     * @param string $name
     *
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function __get($name)
    {
        //if (isset($this->items[$name])) {
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }

        throw new \InvalidArgumentException("Attribute '$name' does not exist.");
    }

    /**
     * **Return a copy of the collection contents.**
     *
     * @return array
     */
    public function copy()
    {
        $copy = [];

        return array_merge_recursive($copy, $this->{'items'});
    }

    /**
     * **Get an attribute.**
     *
     * _Returns NULL if the attribute does not exist._
     *
     * @param string $id
     *
     * @return array|null
     */
    public function getAttribute($id)
    {
        return isset($this->items[$id]) ? $this->items[$id] : NULL;
    }

    /**
     * **Returns an arrayable clone of this class.**
     *
     * @return array
     */
    public function getAttributes()
    {
        return (clone $this);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        //return isset($this->items[$offset]);
        return array_key_exists($offset, $this->items);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * **Set attributes. This will destroy and replace the current items.**
     *
     * @param $attributes
     *
     * @return $this
     *
     * @throws ImmutableViolationException
     */
    public function setAttributes($attributes)
    {
        if (is_array($this->items)) {
            throw new ImmutableViolationException('Cannot use setAttributes once the item array is populated.');
        }

        $this->items = $this->getArrayableItems($attributes);

        return $this;
    }

    /**
     * **Get the collection of items as a PHP array.**
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($value) {
            return is_object($value) && method_exists($value, 'toArray')
                ? $value->toArray()
                : $value;

        }, $this->items);
    }

    /**
     * **Get the collection of items as JSON.**
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->items, $options);
    }

    /**
     * **Returns an array of items from Collection or Arrayable.**
     *
     * @param  mixed $items
     *
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if ($items instanceof self) {
            return $items->copy();
        }

        if (is_object($items) && method_exists($items, 'toArray')) {
            return $items->toArray();
        }

        if (is_object($items) && method_exists($items, 'toJson')) {
            return json_decode($this->toJson(), TRUE);
        }

        return (array) $items;
    }

}
