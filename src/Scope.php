<?php namespace Nine\Collections;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use BadMethodCallException;
use Closure;
use Nine\Traits\WithItemArrayAccess;
use Nine\Traits\WithItemImport;
use Nine\Traits\WithItemTransforms;

/**
 * **Scope is a context container.**
 */
class Scope implements \ArrayAccess, \Countable, \JsonSerializable, ScopeInterface
{
    use WithItemImport;
    use WithItemTransforms;
    use WithItemArrayAccess;

    /** @var array */
    protected $items;

    /** @var array */
    protected $plugins;

    /**
     * **The Scope constructor accepts an array or any arrayable object.**
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = is_array($items) ? $items : $this->getArrayableItems($items);
    }

    /**
     * **Call handler for plugins.**
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if ($this->hasPlugin($method)) {

            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $method = $this->plugins[$method];

            /** @var Closure $method */
            if ($method instanceof Closure) {
                return call_user_func_array($method->bindTo($this, get_class($this)), $parameters);
            }
            else {
                return call_user_func_array($method, $parameters);
            }
        }

        throw new BadMethodCallException("Plug-in {$method} does not exist.");
    }

    /**
     * **The count of items in the Scope.**
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * **Forget a plugin if it exists.**
     *
     * @param string $plugin_name The name of the plugin. May include '.' and '_'
     * @param bool   $sort
     */
    public function forgetPlugin($plugin_name, $sort = FALSE)
    {
        //if (isset($this->plugins[$plugin_name])) {
        if (array_key_exists($plugin_name, $this->plugins)) {
            unset($this->plugins[$plugin_name]);

            // optionally, sort the result
            ! $sort ?: ksort($this->plugins);
        }
    }

    /**
     * **Checks if plugin has been registered.**
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasPlugin($name)
    {
        return array_key_exists($name, (array) $this->plugins);
    }

    /**
     * **Returns all items as a json string.**
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return json_encode($this->items);
    }

    /**
     * **Merge the scope with the provided arrayable items.**
     *
     * @param  mixed $items
     *
     * @return $this
     */
    public function merge($items)
    {
        return $this->items = array_merge($this->items, $this->getArrayableItems($items));
    }

    /**
     * **Register a plugin.**
     *
     * Plugins are stored callable items identifiable by name.
     *
     * @param  string   $name
     * @param  callable $plugin
     *
     * @return void
     */
    public function plugin($name, callable $plugin)
    {
        $this->plugins[$name] = $plugin;
    }

    /**
     *  Sort the internal content array.
     */
    public function sort()
    {
        ksort($this->items);

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
        return json_encode($this->toArray(), $options);
    }

}
