<?php
/**
 * 处理
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-05
 */

namespace App\Consoles;

use App\Librarys\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Kernel extends Console
{
    protected $commands = [
        \App\Consoles\Demo\GreetCommand::class,
        \App\Consoles\Make\MakeCommand::class,
    ];

    protected function check(InputInterface $input, OutputInterface $output)
    {
        // check the environment
        return true;
    }
}