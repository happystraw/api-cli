<?php
/**
 * Application
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App;
use App\Librarys\Container;

class Application extends Container
{
    protected $basePath;

    public function __construct($basePath = null)
    {
        $this->setBasePath($basePath);
        $this->registerBaseBindings();
        $this->registerCoreBindings();
    }

    /**
     * Set base path
     *
     * @param string $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->bindPathsInApplication();
        return $this;
    }

    /**
     * version
     *
     * @return string
     */
    public function version()
    {
        return APP_NAME . ' ' . APP_VERSION;
    }

    /**
     * Bind base servie
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
    }

    /**
     * Bind core service
     */
    protected function registerCoreBindings()
    {
        $bindings = [
            'config' => \App\Librarys\Config::class,
            'lang' => \App\Librarys\Lang::class
        ];
        foreach ($bindings as $alias => $bind) {
            $this->singleton($alias, $bind);
        }
    }

    /**
     * Bind path
     */
    protected function bindPathsInApplication()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.lang', $this->langPath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.bootstrap', $this->bootstrapPath());
        $this->instance('path.console', $this->consolePath());
        $this->instance('path.tpl', $this->tempaltePath());
    }

    /**
     * Init environment
     */
    public function init()
    {
        $config = $this->make('config');
        $config->path($this->configPath())->load('common')->loadConst('const');
        $this->make('lang')->range($config->get('common.lang.range', 'en'))->path($this->langPath())->load('common');
    }

    /**
     * path
     *
     * @return string
     */
    public function path()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'src';
    }

    /**
     * Base path
     *
     * @return mixed
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Bootstrap path
     *
     * @return string
     */
    public function bootstrapPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap';
    }

    /**
     * Configuration path
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Resource path
     *
     * @return string
     */
    public function resourcePath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources';
    }

    /**
     * Language path
     *
     * @return string
     */
    public function langPath()
    {
        return $this->resourcePath() . DIRECTORY_SEPARATOR . 'lang';
    }

    /**
     * Console command file path
     *
     * @return string
     */
    public function consolePath()
    {
        return $this->path() . DIRECTORY_SEPARATOR . 'Consoles';
    }

    /**
     * Template path
     *
     * @return string
     */
    public function tempaltePath()
    {
        return $this->resourcePath() . DIRECTORY_SEPARATOR . 'tpl';
    }


}