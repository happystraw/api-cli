<?php
/**
 * Command
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-05
 */

namespace App\Librarys;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends ConsoleCommand
{
    protected $retry = 0;
    protected $maxRetry = 3;

    protected function retry($success = true, OutputInterface $output = null)
    {
        if ($success) {
            $this->retry = 0;
        } else {
            $this->retry ++;
            if ($this->retry >= $this->maxRetry) {
                $output && $output->write(lang('consoles.maxretry'));
                return false;
            }
        }
        return true;
    }

    /**
     * Get command language
     *
     * @param string $name
     * @return mixed
     */
    protected function lang($name)
    {
        return strpos($name, '.') ? lang($name) : lang('consoles.' . $this->getName() . '.' . $name);
    }

    protected function writeln(OutputInterface $output, $msg, $type = '', $options = 0)
    {
        if ($type) {
            $output->writeln("<{$type}>{$msg}</{$type}>", $options);
        } else {
            $output->writeln($msg, $options);
        }
    }
}