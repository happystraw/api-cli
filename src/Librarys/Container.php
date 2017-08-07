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
     * 容器单例
     *
     * @var static
     */
    protected static $instance;

    /**
     * 绑定服务
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * 单例数组
     *
     * @var array
     */
    protected $instances = [];

    /**
     * 是否绑定服务
     *
     * @param $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    /**
     * 实现绑定注册
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
     * 获取绑定信息
     *
     * @param $abstract
     * @return mixed|null
     */
    public function getBinding($abstract)
    {
        return isset($this->bindings[$abstract]) ? $this->bindings[$abstract] : null;
    }

    /**
     * 绑定服务
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
     * 注销绑定
     *
     * @param string $abstact
     */
    public function unbind($abstact)
    {
        unset($this->bindings[$abstact]);
    }

    /**
     * 获取全部绑定
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * 设置单例
     *
     * @param string $abstract
     * @param mixed $instance
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance instanceof Closure ? $instance() : $instance;
    }

    /**
     * 设置单例 -- 不做不处理
     *
     * @param string $abstract
     * @param mixed $concrete
     * @param mixed $params
     */
    public function singleton($abstract, $concrete, $params = null)
    {
        $this->bind($abstract, function () use ($concrete, $params) {
            if (is_null($params)) return new $concrete();
            return new $concrete($params);
        });
    }

    /**
     * 移除单例
     *
     * @param string $abstract
     */
    public function removeInstance($abstract)
    {
        unset($this->instances[$abstract]);
    }

    /**
     * 清空单例
     */
    public function clearInstances()
    {
        $this->instances = [];
    }

    /**
     * 清空
     */
    public function flush()
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * 获取全部单例
     *
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * 返回自身
     *
     * @return Container
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * 设置自身单例
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