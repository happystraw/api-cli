<?php
/**
 * Config Facade
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-04
 */

namespace App\Facades;

class Config extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}