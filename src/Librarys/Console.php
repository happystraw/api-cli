<?php
/**
 * Console
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-05
 */

namespace App\Librarys;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Console extends Application
{
    protected $commands = [];

    public function __construct($name = APP_NAME, $version = APP_VERSION)
    {
        parent::__construct($name, $version);
        $this->initCommands();
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) $input = new ArgvInput();
        if (null === $output) $output = new ConsoleOutput();
        if (!$this->check($input, $output)) exit;
        return parent::run($input, $output);
    }

    /**
     * Check environment
     *
     * @param OutputInterface $output
     * @return mixed
     */
    protected function check(InputInterface $input, OutputInterface $output)
    {
        return true;
    }

    /**
     * Init
     */
    protected function initCommands()
    {
        foreach ($this->commands as $command) {
            $this->add(new $command);
        }
    }
}