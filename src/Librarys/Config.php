<?php
/**
 * Config
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App\Librarys;

use App\Traits\SingletonTraits;
use Exception;

final class Config
{
    use SingletonTraits;
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

    private function __construct() {}

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
     * @param string $file 目录或文件
     * @param string|null $path 目录
     * @return self
     * @throws Exception
     */
    public function load($file, $path = null)
    {
        $file = $this->parseFilePath($file, $path);
        if (is_dir($file)) {
            $dirHandle = opendir($file);
            while (FALSE !== ($filename = readdir($dirHandle))) {
                $pathinfo = pathinfo($filename);
                if ($pathinfo['extension'] === 'php') {
                    $this->set(include $file . DIRECTORY_SEPARATOR . $filename, $pathinfo['filename']);
                }
            }
            closedir($dirHandle);
        } elseif (is_file($file)) {
            $pathinfo = pathinfo($file);
            if ($pathinfo['extension'] === 'php') {
                $this->set(include $file, $pathinfo['filename']);
            } else {
                throw new Exception("Only Support *.php extension");
            }
        } else {
            throw new Exception("FILE/DIR[{$file}] not Exists");
        }
        $this->configFiles[] = $file;
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
    public function get($name = null, $default = null)
    {
        if (empty($name)) {
            return $this->configs;
        } else {
            // 二维数组配置
            if (!strpos($name, '.')) {
                return isset($this->configs[$name]) ? $this->configs[$name] : $default;
            } else {
                $array = explode('.', $name);
                if (count($array) == 2) return isset($this->configs[$array[0]][$array[1]]) ? $this->configs[$array[0]][$array[1]] : $default;
                if (!isset($this->configs[$array[0]]) || !is_array($this->configs[$array[0]])) return $default;
                $initial = $this->configs[$array[0]];
                $last = end($array);
                unset($array[0]);
                return array_reduce($array, function ($carry, $item) use ($last, $default) {
                    return isset($carry[$item]) ? $carry[$item] : ($last == $item ? $default : null);
                }, $initial);
            }
        }
    }

    /**
     * Set configuration
     *
     * @param array|string $name
     * @param mixed $value
     * @return mixed
     */
    public function set($name, $value = null)
    {
        // set value by string
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                $this->configs[$name] = $value;
            } else {
                // set recursion with '.'
                $array = explode('.', $name);
                if (count($array) == 2) {
                    $this->configs[$array[0]][$array[1]] = $value;
                } else {
                    $array = array_reverse($array);
                    $this->configs = array_replace_recursive($this->configs, array_reduce($array, function ($carry, $item) {
                        return [$item => $carry];
                    }, $value));
                }
            }
        } elseif (is_array($name)) {
            // batch set
            if (!empty($value)) {
                $this->configs[$value] = isset($this->configs[$value])
                    ? array_merge($this->configs[$value], $name)
                    : $name;
            } else {
                $this->configs = array_merge($this->configs, $name);
            }
        } else {
            // return
            return $this->configs;
        }
        return null;
    }

    /**
     * Determine whether the configuration exists
     *
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        if (!strpos($name, '.')) {
            return isset($this->configs[$name]);
        } else {
            $array = explode('.', $name);
            if (count($array) == 2) return isset($this->configs[$array[0]][$array[1]]);
            if (!isset($this->configs[$array[0]])) return false;
            $initial = $this->configs[$array[0]];
            $last = end($array);
            unset($array[0]);
            return array_reduce($array, function ($carry, $item) use ($last) {
                if ($last == $item) return isset($carry[$item]);
                return isset($carry[$item]) ? $carry[$item] : false;
            }, $initial);
        }
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
        foreach ($this->configFiles as $file) {
            $this->load($file, '');
        }
    }
}