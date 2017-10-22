<?php
/**
 * Load File Traits
 *
 * @author: FangYutao <fangyutao@star-net.cn>
 * @since : 2017-10-22
 */

namespace App\Traits;

trait LoadFileTraits
{
    /**
     * Load file
     *
     * @param string $filename
     * @param bool $withKey
     * @return mixed
     */
    public function file($filename, $withKey = true)
    {
        if (file_exists($filename)) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $content = include $filename;
            return $withKey ? [$name => $content] : $content;
        }
        return null;
    }
}