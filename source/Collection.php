<?php
namespace Arakxz\Database;

class Collection implements \Countable, \Iterator
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $collection = array();

    /**
     * Create a new collection.
     *
     * @param  array  $items
     *
     * @return void
     */
    public function __construct(array $collection = array())
    {
        $this->collection = $collection;
    }

    public function __destruct()
    { }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     *
     * @return $this
     */
    public function add($item)
    {
        $this->collection[] = $item;

        return $this;
    }

    /**
     * Execute a callback over each item.
     *
     * @param  \Closure  $callback
     *
     * @return $this
     */
    public function each(\Closure $callback)
    {
        array_map($callback, $this->collection);

        return $this;
    }

    /**
     * Run a map over each of the items.
     *
     * @param  \Closure  $callback
     *
     * @return static
     */
    public function map(\Closure $callback)
    {
        return new static(array_map($callback, $this->collection));
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  \Closure  $callback
     *
     * @return static
     */
    public function filter(\Closure $callback)
    {
        return new static(array_filter($this->collection, $callback));
    }

    /**
     * Get the first item from the collection.
     *
     * @return mixed | null
     */
    public function first()
    {
        return reset($this->collection);
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->collection);
    }

    /**
     * Get a item from the collection.
     *
     * @param  integer $index
     *
     * @return mixed
     */
    public function item($index)
    {
        return $this->collection[$index];
    }

    /**
     * Get the last item from the collection.
     *
     * @return mixed | null
     */
    public function last()
    {
        return end($this->collection);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return integer
     */
    public function length()
    {
        return $this->count();
    }

    /**
     * Get and remove the last item from the collection.
     *
     * @return mixed | null
     */
    public function pop()
    {
        return array_pop($this->collection);
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param  mixed  $value
     *
     * @return void
     */
    public function prepend($value)
    {
        array_unshift($this->collection, $value);
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param  mixed  $item
     *
     * @return $this
     */
    public function push($item)
    {
        $this->collection[] = $item;

        return $this;
    }

    /**
     * Reverse items order.
     *
     * @param  boolean $preserve
     *
     * @return static
     */
    public function reverse($preserve = false)
    {
        return new static(array_reverse($this->collection, $preserve));
    }

    /**
     * @param  array  $collection
     *
     * @return $this
     */
    public function replace(array $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get and remove the first item from the collection.
     *
     * @return mixed | null
     */
    public function shift()
    {
        return array_shift($this->collection);
    }

    /**
     * Slice the underlying collection array.
     *
     * @param  int   $offset
     * @param  int   $length
     * @param  bool  $preserveKeys
     *
     * @return static
     */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return new static(array_slice($this->collection, $offset, $length, $preserveKeys));
    }

    /**
     * Sort through each item with a callback.
     *
     * @param  \Closure  $callback
     *
     * @return $this
     */
    public function sort(\Closure $callback)
    {
        uasort($this->collection, $callback);

        return $this;
    }

    /**
     * Chunk the underlying collection array.
     *
     * @param  int   $size
     * @param  bool  $preserveKeys
     *
     * @return static
     */
    public function chunk($size, $preserveKeys = false)
    {
        $chunks = new static;

        foreach (array_chunk($this->collection, $size, $preserveKeys) as $chunk)
        {
            $chunks->push(new static($chunk));
        }

        return $chunks;
    }

    /**
     * Transform each item in the collection using a callback.
     *
     * @param  \Closure  $callback
     *
     * @return $this
     */
    public function transform(\Closure $callback)
    {
        $this->collection = array_map($callback, $this->collection);

        return $this;
    }

    /**
     * Splice portion of the underlying collection array.
     *
     * @param  integer $offset
     * @param  integer $length
     * @param  mixed   $replacement
     *
     * @return static
     */
    public function splice($offset, $length = 0, $replacement = array())
    {
        return new static(array_splice($this->collection, $offset, $length, $replacement));
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->collection;
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->collection, $options);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->collection);
    }

    /**
     * @return scalar
     */
    public function key()
    {
        return key($this->collection);
    }

    /**
     * @return void
     */
    public function next()
    {
        next($this->collection);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        reset($this->collection);
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return key($this->collection) !== null;
    }

}
