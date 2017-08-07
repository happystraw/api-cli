<?php
/**
 * 配置静态转发
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App\Facades;
use App\Librarys\Config as realConfig;

class Config extends Facade
{
    protected static function getFacadeAccessor()
    {
        return realConfig::instance();
    }
}