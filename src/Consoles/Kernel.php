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
        \App\Consoles\App\InitCommand::class,
    ];

    protected function check(InputInterface $input, OutputInterface $output)
    {
        $path = config('common.project.path');
        if ($path && is_dir($path)) return true;
        $output->writeln(lang('consoles.path_not_exist')?:'path not exist!');
        return false;
    }
}