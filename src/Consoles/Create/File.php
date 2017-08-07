<?php
/**
 * File.php
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-05
 */

namespace App\Consoles\Create;
//use Symfony\Component\Console\Command\Command;
use App\Librarys\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class File extends Command
{
    protected function configure()
    {
        $this
            ->setName('demo:greet')
            ->setDescription('Greet someone')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addArgument(
                'xxxx',
                InputArgument::OPTIONAL,
                'xxxx?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with this action?', false);
        $str = $helper->ask($input, $output, $question);
        dd($str);
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln("<error>{$text}</error>");
        $output->writeln(
            'Will only be printed in verbose mode or higher',
            OutputInterface::VERBOSITY_VERBOSE
        );
    }
}