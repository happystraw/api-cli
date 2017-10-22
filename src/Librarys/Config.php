<?php
/**
 * Config
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App\Librarys;

use App\Traits\LoadFileTraits;
use App\Traits\SingletonTraits;
use App\Utils\Arr;
use Exception;
use ArrayAccess;

class Config implements ArrayAccess
{
    use SingletonTraits;
    use LoadFileTraits;
    /**
     * Global configuration
     *
     * @var array
     */
    private $configs = [];

    /**
     * Configuration file
     *
     * @var array
     */
    private $configFiles = [];

    /**
     * Base Path
     *
     * @var string
     */
    private $path = '';

    /**
     * File extension
     *
     * @var string
     */
    private $fileExt = 'php';

    /**
     * Set base path
     *
     * @param string $path
     * @return self|string
     */
    public function path($path = null)
    {
        if (is_null($path)) return $this->path;
        $this->path = rtrim($path, '\/');
        return $this;
    }

    /**
     * Set file extension
     *
     * @param string $ext
     * @return $this
     */
    public function setFileExt($ext)
    {
        $this->fileExt = $ext;
        return $this;
    }

    /**
     * Join file path
     *
     * @param string $file
     * @param string $path
     * @return string
     */
    protected function parseFilePath($file, $path)
    {
        $path = is_null($path) ? $this->path : rtrim($path, '\/');
        $realPath = $path . DIRECTORY_SEPARATOR . ltrim($file, '\/');
        return $this->fileExt ? ($realPath . '.' . $this->fileExt) : $realPath;
    }

    /**
     * Load file
     *
     * @param string|array $file
     * @param string|null $path
     * @return self
     * @throws Exception
     */
    public function load($file, $path = null)
    {
        $file = (array)$file;
        foreach ($file as $f) {
            $filename = $this->parseFilePath($f, $path);
            if (($values = $this->file($filename))) $this->set($values);
        }
        $this->configFiles[] = ['file' => $file, 'path' => $path];
        return $this;
    }

    /**
     * load global configuration
     *
     * @param string $file
     * @param string|null $path
     * @return self
     * @throws Exception
     */
    public function loadConst($file, $path = null)
    {
        $file = $this->parseFilePath($file, $path);
        if (file_exists($file)) {
            include $file;
        } else {
            throw new Exception("FILE[{$file}] not Exists", 1);
        }
        return $this;
    }

    /**
     * Get configuration
     *
     * @param string $name
     * @param string $default default value if the $name not exists
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return Arr::get($this->configs, $name, $default);
    }

    /**
     * Set configuration
     *
     * @param array|string $name
     * @param mixed $value
     * @return self
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            // batch set
            foreach ($name as $innerKey => $innerValue) {
                Arr::set($this->configs, $innerKey, $innerValue);
            }
        } elseif (is_null($value)) {
            // unset value
            Arr::forget($this->configs, $name);
        } elseif (is_string($name)) {
            // set value by string
            Arr::set($this->configs, $name, $value);
        }
        return $this;
    }

    /**
     * Determine whether the configuration exists
     *
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        return Arr::has($this->configs, $name);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->configs;
    }

    /**
     * Reset
     */
    public function reset()
    {
        $this->configs = [];
    }

    /**
     * Reload Files
     */
    public function reload()
    {
        foreach ($this->configFiles as $config) {
            $this->load($config['file'], $config['path']);
        }
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}