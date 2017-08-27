<?php
/**
 * SingletonTraits
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */


namespace App\Traits;
use Exception;

trait SingletonTraits
{
    protected static $instance = null;

    /**
     * Create Singleton
     *
     * @param array $options
     * @return static
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            static::$instance = new static($options);
        }
        return static::$instance;
    }

    /**
     * Magic Method, Static call methods
     *
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $params)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        if (is_callable([static::$instance, $method])) {
            return call_user_func_array([static::$instance, $method], $params);
        } else {
            throw new Exception("method not exists:" . $method);
        }
    }
}