<?php
/**
 * 语言静态转发
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App\Facades;
use App\Librarys\Lang as realLang;

class Lang extends Facade
{
    protected static function getFacadeAccessor()
    {
        return realLang::instance();
    }
}