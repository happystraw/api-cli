<?php
/**
 * IoC
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App\Librarys;
use Closure;
use ArrayAccess;

class Container implements ArrayAccess
{
    /**
     * Container instance
     *
     * @var static
     */
    protected static $instance;

    /**
     * Bind service
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * Array of instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Check a service was bound or not bound
     *
     * @param $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    /**
     * Make a instance
     *
     * @param string $abstract
     * @return mixed|null
     */
    public function make($abstract)
    {
        if (isset($this->instances[$abstract])) return $this->instances[$abstract];
        if ($bind = $this->getBinding($abstract)) {
            $this->instances[$abstract] = $bind();
            return $this->instances[$abstract];
        }
        return null;
    }

    /**
     * Get the Bindings
     *
     * @param $abstract
     * @return mixed|null
     */
    public function getBinding($abstract)
    {
        return isset($this->bindings[$abstract]) ? $this->bindings[$abstract] : null;
    }

    /**
     * Bind Service
     *
     * @param string $abstract
     * @param Closure|null $concrete
     */
    public function bind($abstract, Closure $concrete = null)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete instanceof Closure ? $concrete : function () use ($concrete) {
            return $concrete;
        };
    }

    /**
     * Drop the Service which was bound
     *
     * @param string $abstact
     */
    public function unbind($abstact)
    {
        unset($this->bindings[$abstact]);
    }

    /**
     * Get All Bindings
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Set instance
     *
     * @param string $abstract
     * @param mixed $instance
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance instanceof Closure ? $instance() : $instance;
    }

    /**
     * Bind instance -- without parsing
     *
     * @param string $abstract
     * @param string $class
     * @param mixed $params
     */
    public function singleton($abstract, $class, $params = null)
    {
        $this->bind($abstract, function () use ($class, $params) {
            if (isset(class_uses_recursive($class)[\App\Traits\SingletonTraits::class])) {
                return is_null($params) ? $class::instance() : $class::instance($params);
            } else {
                return is_null($params) ? new $class() : new $class($params);
            }
        });
    }

    /**
     * Remove instance
     *
     * @param string $abstract
     */
    public function removeInstance($abstract)
    {
        unset($this->instances[$abstract]);
    }

    /**
     * Clear all instances
     */
    public function clearInstances()
    {
        $this->instances = [];
    }

    /**
     * Empty all binding and instances
     */
    public function flush()
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * Get all instanes
     *
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * Return self
     *
     * @return Container
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Set Self
     *
     * @param Container|null $app
     * @return Container
     */
    public static function setInstance(Container $app = null)
    {
        return static::$instance = $app;
    }

    public function offsetExists($key)
    {
        return $this->bound($key);
    }

    public function offsetGet($key)
    {
        return $this->make($key);
    }

    public function offsetSet($key, $value)
    {
        $this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    public function offsetUnset($key)
    {
        unset($this->bindings[$key], $this->instances[$key]);
    }

    public function __get($key)
    {
        return $this[$key];
    }

    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}