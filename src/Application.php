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
    const VERSION = '1.0.0';
    protected $basePath;

    public function __construct($basePath = null)
    {
        $this->setBasePath($basePath);
        $this->registerBaseBindings();
        $this->registerCoreBindings();
    }

    /**
     * 设置基础路径
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
     * 版本
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * 绑定基础服务
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
    }

    /**
     * 绑定核心服务
     */
    protected function registerCoreBindings()
    {
        $bindings = [
            'config' => \App\Librarys\Config::instance(),
            'lang' => \App\Librarys\Lang::instance()
        ];
        foreach ($bindings as $alias => $bind) {
            $this->instance($alias, $bind);
        }
    }

    /**
     * 绑定路径
     */
    protected function bindPathsInApplication()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.lang', $this->langPath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.bootstrap', $this->bootstrapPath());
    }

    /**
     * 初始化环境
     */
    public function initEnv()
    {
        $config = $this->make('config');
        $config->path($this->configPath())->load('common')->loadConst('base');
        $this->make('lang')->range($config->get('common.lang.range', 'zh-cn'))->path($this->langPath())->load('common');
    }

    /**
     * 检查环境
     */
    public function check()
    {
        if (!IS_CLI) exit($this->make('lang')->get('common.not_cli'));
        if (PHP_VERSION < APP_PHP_VERSION) exit($this->make('lang')->get('common.low_php'));
    }

    /**
     * 代码路径
     *
     * @return string
     */
    public function path()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'src';
    }

    /**
     * 基础路径
     *
     * @return mixed
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * 启动目录
     *
     * @return string
     */
    public function bootstrapPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap';
    }

    /**
     * 配置目录
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * 资源文件路径
     *
     * @return string
     */
    public function resourcePath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources';
    }

    /**
     * 语言参数路径
     *
     * @return string
     */
    public function langPath()
    {
        return $this->resourcePath() . DIRECTORY_SEPARATOR . 'lang';
    }


}