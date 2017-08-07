<?php
use App\Application;

if (!function_exists('app')) {
    /**
     * 获取应用
     *
     * @param string|null $abstract
     * @return \App\Librarys\Container|mixed|null
     */
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return Application::getInstance();
        }
        return Application::getInstance()->make($abstract);
    }
}

if (!function_exists('config')) {
    /**
     * 获取 / 设置 应用的配置值
     *
     * @param  array|string  $key
     * @param  mixed  $default 默认值
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
     * 获取 / 设置 语言的配置值
     *
     * @param  array|string  $key
     * @param  array $vars 绑定值
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

if (! function_exists('dd')) {
    /**
     * 打印数据
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