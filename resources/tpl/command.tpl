<?php
/**
 * {{ title }}
 */

namespace App\Consoles\{{ moduleName }};
use App\Librarys\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class {{ className }} extends Command
{
    protected function configure()
    {
        $this
            ->setName('{{ module }}:{{ action }}')
            ->setDescription('Description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Command For ' . $this->getName() . '. At ' . __FILE__ . '.</info>');
    }
}