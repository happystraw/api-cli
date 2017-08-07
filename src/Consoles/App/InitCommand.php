<?php
/**
 * InitCommand.php
 * @author: FangYutao <fangyutao1993@hotmail.com>
 * @since : 2017-08-06
 */

namespace App\Consoles\App;


use App\Librarys\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class InitCommand extends Command
{
    protected $config = [];

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->config = config('common') ?: [];
    }

    protected function configure()
    {
//        dd(lang('consoles.app:init.description'));
        $this->setName('app:init')
            ->setDescription($this->lang('description'))
            ->setHelp($this->lang('help'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $this->setPath($helper, $input, $output);
        if(!file_put_contents(
            app('path.config') . DIRECTORY_SEPARATOR . 'test.php',
            "<?php\nreturn " . var_export(config('common'), true) . ';')
        ) {
            $output->writeln($this->lang('init_fail'));
        };
    }

    protected function setPath(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
    {
        $question = new Question($this->lang('set_path'));
        $path = trim($helper->ask($input, $output, $question));
        if ($path) {
            if (is_dir($path)) {
                $this->retry(true);
                app('config')->set('common.project.path', $path);
            } else {
                $output->writeln($this->lang('path_invalid'));
                if (!$this->retry(false, $output)) exit();
                $this->setPath($helper, $input, $output);
            }
        } else {
            if (!$this->retry(false, $output)) exit();
            $this->setPath($helper, $input, $output);
        }
    }
}