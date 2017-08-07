<?php
/**
 * 静态引用转发
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App\Facades;
use Exception;

abstract class Facade
{
    /**
     * 单例
     *
     * @var array
     */
    protected static $facades = [];

    /**
     * 获取转发对象
     *
     * @throws Exception
     */
    protected static function getFacadeAccessor()
    {
        throw new Exception('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * 生成单例
     *
     * @param string|object $class
     * @return mixed
     */
    protected static function resolveFacadeInstance($class)
    {
        if (is_object($class)) {
            return $class;
        } else {
            return isset(static::$facades[$class]) ? static::$facades[$class] : (static::$facades[$class] = new $class());
        }
    }

    /**
     * 获取单例
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();
        if (!$instance) {
            throw new Exception('A facade root has not been set.');
        }
        switch (count($args)) {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array([$instance, $method], $args);
        }
    }
}