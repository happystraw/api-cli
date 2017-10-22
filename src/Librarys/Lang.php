<?php
/**
 * Lang
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-05
 */

namespace App\Librarys;

use App\Traits\LoadFileTraits;
use App\Traits\SingletonTraits;
use App\Utils\Arr;
use ArrayAccess;

class Lang implements ArrayAccess
{
    use SingletonTraits;
    use LoadFileTraits;

    /**
     * @var array
     */
    private $lang = [];

    /**
     * Language zone
     *
     * @var string
     */
    private $range = 'en';

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
    public function load($file, $path = null, $range = '')
    {
        $file = (array)$file;
        foreach ($file as $f) {
            $filename = $this->parseFilePath($f, $path, $range);
            if (($values = $this->file($filename))) $this->set($values);
        }
        return $this;
    }

    /**
     * Set value
     *
     * @param $name
     * @param mixed $value
     * @param string $range
     * @return self
     */
    public function set($name, $value = null, $range = '')
    {
        $this->lang[$range] = $this->all($range);
        if (is_array($name)) {
            // batch set
            foreach ($name as $innerKey => $innerValue) {
                Arr::set($this->lang[$range], $innerKey, $innerValue);
            }
        } elseif (is_null($value)) {
            Arr::forget($this->lang[$range], $name);
        } else {
            Arr::set($this->lang[$range], $name, $value);
        }
        return $this;
    }

    /**
     * Get value
     *
     * @param string $name
     * @param array $vars Bind values
     * @param string $range
     * @return mixed|null
     */
    public function get($name, $vars = [], $range = '')
    {
        $value = Arr::get($this->all($range), $name);
        // parse params & format
        if ($value && is_array($vars) && $vars) {
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
        return Arr::has($this->all($range), $name);
    }

    /**
     * Get all items in range
     *
     * @param string $range
     * @return array|mixed
     */
    public function all(&$range = '')
    {
        $range = $range ?: $this->range;
        return isset($this->lang[$range]) ? $this->lang[$range] : [];
    }

    /**
     * Get all items without range
     *
     * @return array
     */
    public function total()
    {
        return $this->lang;
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