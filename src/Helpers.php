<?php
use App\Librarys\Container;

if (!function_exists('app')) {
    /**
     * Get the Application
     *
     * @param string|null $abstract
     * @return \App\Librarys\Container|mixed|null
     */
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }
        return Container::getInstance()->make($abstract);
    }
}

if (!function_exists('config')) {
    /**
     * Get / Set the Config
     *
     * @param  array|string $key
     * @param  mixed $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }
        if (is_array($key)) {
            return app('config')->set($key);
        }
        return app('config')->get($key, $default);
    }
}

if (!function_exists('lang')) {
    /**
     * Get / Set the Language of Application
     *
     * @param  array|string $key
     * @param  array $vars
     * @return mixed
     */
    function lang($key = null, $vars = null)
    {
        if (is_null($key)) {
            return app('lang');
        }
        if (is_array($key)) {
            return app('lang')->set($key);
        }
        return app('lang')->get($key, $vars);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump vars
     *
     * @param array ...$params
     */
    function dd(...$params)
    {
        array_map(function ($x) {
            var_dump($x);
        }, $params);
        die(1);
    }
}

if (!function_exists('class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its subclasses and trait of their traits.
     *
     * @param  string $class
     * @return array
     */
    function class_uses_recursive($class)
    {
        $results = [];

        foreach (array_merge([$class => $class], class_parents($class)) as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (!function_exists('trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  string $trait
     * @return array
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}

if (!function_exists('windows_os')) {
    /**
     * Determine whether the current environment is Windows based.
     *
     * @return bool
     */
    function windows_os()
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
    }
}