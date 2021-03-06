<?php
/**
 * Kernel
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-05
 */

namespace App\Consoles;

use App\Librarys\Console;

class Kernel extends Console
{
    protected $commands = [
        \App\Consoles\Demo\GreetCommand::class,
        \App\Consoles\Make\MakeCommand::class,
    ];
}