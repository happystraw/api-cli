<?php
/**
 * Lang
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-05
 */

namespace App\Librarys;

use App\Traits\SingletonTraits;

final class Lang
{
    use SingletonTraits;

    /**
     * @var array
     */
    private $lang = [];

    /**
     * Language zone
     *
     * @var string
     */
    private $range = 'zh-cn';

    /**
     * The base path of language config file
     *
     * @var string
     */
    private $path = '';

    /**
     * Config file extension
     *
     * @var string
     */
    private $fileExt = 'php';

    private function __construct() {}

    /**
     * Set / Get language's range
     *
     * @param null|string $range
     * @return $this|string
     */
    public function range($range = null)
    {
        if (is_null($range)) return $this->range;
        $this->range = $range;
        return $this;
    }

    /**
     * Set / Get base path
     *
     * @param null|string $path
     * @return $this|string
     */
    public function path($path = null)
    {
        if (is_null($path)) return $this->path;
        $this->path = rtrim($path, '\/');
        return $this;
    }

    /**
     * Set config file extension
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
     * Join the file path
     *
     * @param string $file
     * @param string $path
     * @param string $range
     * @return string
     */
    protected function parseFilePath($file, $path, $range)
    {
        $path = is_null($path) ? $this->path : rtrim($path, '\/');
        $range = $range ?: $this->range;
        $realPath = $path . DIRECTORY_SEPARATOR . $range . DIRECTORY_SEPARATOR . ltrim($file, '\/');
        return $this->fileExt ? ($realPath . '.' . $this->fileExt) : $realPath;
    }

    /**
     * Load files
     *
     * @param string $file
     * @param string|null $path
     * @param string $range
     * @return mixed
     */
    public function load($file, $path = null, $range = null)
    {
        $range = $range ?: $this->range;
        if (!isset($this->lang[$range])) {
            $this->lang[$range] = [];
        }
        // multi define
        if (is_string($file)) {
            $file = [$file];
        }
        $lang = [];
        foreach ($file as $_file) {
            $_file = $this->parseFilePath($_file, $path, $range);
            if (file_exists($_file)) {
                $_name = pathinfo($_file)['filename'];
                $_lang = include $_file;
                if (is_array($_lang)) {
                    $lang[$_name] = array_change_key_case($_lang);
                }
            }
        }
        if (!empty($lang)) {
            $this->lang[$range] = $lang + $this->lang[$range];
        }
        return $this;
    }

    /**
     * Set value
     *
     * @param $name
     * @param null $value
     * @param string $range
     * @return self
     */
    public function set($name, $value = null, $range = '')
    {
        $range = $range ?: $this->range;
        if (!isset($this->lang[$range])) {
            $this->lang[$range] = [];
        }
        if (is_array($name)) {
            $this->lang[$range] = array_change_key_case($name) + $this->lang[$range];
        } else {
            $name = strtolower($name);
            if (!strpos($name, '.')) {
                $this->lang[$range][$name] = $value;
            } else {
                // 多维数组配置
                $array = explode('.', $name);
                if (count($array) == 2) {
                    $this->lang[$range][$array[0]][$array[1]] = $value;
                } else {
                    $array = array_reverse($array);
                    $this->lang[$range] = array_replace_recursive($this->lang[$range], array_reduce($array, function ($carry, $item) {
                        return [$item => $carry];
                    }, $value));
                }
            }
        }
        return $this;
    }

    /**
     * Get value
     *
     * @param null|string $name
     * @param array $vars
     * @param string $range
     * @return mixed|null
     */
    public function get($name = null, $vars = [], $range = '')
    {
        $range = $range ?: $this->range;
        // if $name is empty, return all
        if (empty($name)) {
            return isset($this->lang[$range]) ? $this->lang[$range] : null;
        }
        $name = strtolower($name);
        if (!strpos($name, '.')) {
            $value = isset($this->lang[$range][$name]) ? $this->lang[$range][$name] : null;
        } else {
            $array = explode('.', $name);
            if (count($array) == 2) {
                if (!isset($this->lang[$range][$array[0]][$array[1]])) return null;
                $value = $this->lang[$range][$array[0]][$array[1]];
            } else {
                if (!isset($this->lang[$range][$array[0]]) || !is_array($this->lang[$range][$array[0]])) return null;
                $initial = $this->lang[$range][$array[0]];
                unset($array[0]);
                $value = array_reduce($array, function ($carry, $item) {
                    return isset($carry[$item]) ? $carry[$item] : null;
                }, $initial);
            }
        }
        // parse params
        if ($value && !empty($vars) && is_array($vars)) {
            if (key($vars) === 0) {
                // numeric index
                array_unshift($vars, $value);
                $value = call_user_func_array('sprintf', $vars);
            } else {
                // Association index parsing
                $replace = array_keys($vars);
                foreach ($replace as &$v) {
                    $v = ":{$v}";
                }
                $value = str_replace($replace, $vars, $value);
            }

        }
        return $value;
    }

    /**
     * Determine whether or not the value
     *
     * @param string $name
     * @param string $range
     * @return bool
     */
    public function has($name, $range = '')
    {
        $range = $range ?: $this->range;
        return isset($this->lang[$range][strtolower($name)]);
    }
}