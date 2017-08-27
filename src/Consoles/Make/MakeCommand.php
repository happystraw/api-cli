<?php
/**
 * New a Command File
 *
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-26
 */

namespace App\Consoles\Make;
use App\Librarys\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    protected $text = '';

    protected function configure()
    {
        $this
            ->setName('make:command')
            ->setDescription('Create A New Command File')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of new command. format: <ModuleName>/<ClassName>; e.g. User/Create'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $name = $input->getArgument('name');
        $this->createCommand($name);
        $output->writeln($this->text);
    }

    protected function createCommand($name)
    {
        if(!preg_match('/^[A-Za-z][A-Za-z0-9_]+\/[A-Za-z0-9_]+$/', $name)) {
            return $this->setStatus(false, 'The name is illegal');
        }
        $arrNames = explode('/', $name);
        $moduleName = $arrNames[0];
        $className = $arrNames[1];
        if (!($filepath = $this->makeCommandDir($moduleName))) return false;
        if (!$this->parseCommandFile($filepath, $moduleName, $className)) return false;
        return $this->addToKernel($moduleName, $className);
    }

    protected function makeCommandDir($module)
    {
        $filepath = app('path.console') . DIRECTORY_SEPARATOR . $module;
        if (!is_dir($filepath) && mkdir($filepath, 0777, true)) {
            return $filepath;
        } else if (is_writable($filepath)) {
            return $filepath;
        } else {
            return $this->setStatus(false, 'The command directory is not writable');
        }
    }

    protected function parseCommandFile($filepath, $module, $class)
    {
        $filename = $filepath. DIRECTORY_SEPARATOR . "{$class}Command.php";
        if (file_exists($filename)) return $this->setStatus(false, 'The command file exists');
        $tpl = file_get_contents(app('path.tpl') . DIRECTORY_SEPARATOR . 'command.tpl');
        $tpl = str_replace([
            '{{ title }}',
            '{{ moduleName }}',
            '{{ className }}',
            '{{ module }}',
            '{{ action }}',
        ], [
            "{$module} {$class}",
            $module,
            $class,
            strtolower($module),
            strtolower($class),
        ], $tpl);
        if (file_put_contents($filepath. DIRECTORY_SEPARATOR . "{$class}Command.php", $tpl)) return $this->setStatus();
        else $this->setStatus(false, 'The command file is not writable');
    }

    protected function addToKernel($module, $class)
    {
        $namespace = "\\App\\Consoles\\{$module}\\{$class}Command::class,";
        $kernelFile = app('path.console') . DIRECTORY_SEPARATOR . 'Kernel.php';
        $status = false;
        $content = preg_replace_callback(
            '/protected \$commands([\s\S\w]*\];)/',
            function ($matches) use ($namespace, &$status) {
                $commands = str_replace(["\r", "\n", '[', ']', '=', ';', ' '], '', $matches[1]);
                $commands = explode(',', trim($commands, ','));
                $commands[] = $namespace;
                $status = true;
                return "protected \$commands = [\r\n" . implode(",\r\n", array_map(function ($v) {
                    return '        ' . $v;
                }, $commands)) . "\r\n    ];";
            },
            file_get_contents($kernelFile)
        );
        if (!$status) $this->setStatus(false, 'The command kernel file\'s format is invalid');
        if (file_put_contents($kernelFile, $content)) return $this->setStatus();
        else $this->setStatus(false, 'The command kernel file is not writable');
    }

    protected function setStatus($success = true, $message = 'File Created')
    {
        $type = $success ? 'info' : 'error';
        $this->text = "<{$type}>{$message}</{$type}>";
        return $success;
    }
}