<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Engine\Coroutine;
use Hyperf\Support\Composer;
use InvalidArgumentException;
use Hyperf\Server\ServerFactory;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Swoole\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Hyperf\Support\swoole_hook_flags;
use function Hyperf\Support\env;

#[Command]
class RestartCommand extends HyperfCommand
{
    private $daemonize;
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('restart');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Start hyperf servers.')
            ->addOption('watch', 'w', InputOption::VALUE_OPTIONAL, 'watch swoole server', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int{
        $this->checkEnvironment($output);
        $this->stopServer();
//        $this->startServer();
//        if ($input->getOption('watch') !== false) {
//            $this->watchServer();
//        }else{
//
//        }
        return 0;
    }


    private function startServer()
    {
        $serverFactory = $this->container->get(ServerFactory::class)
            ->setEventDispatcher($this->container->get(EventDispatcherInterface::class))
            ->setLogger($this->container->get(StdoutLoggerInterface::class));
        $serverConfig = $this->container->get(ConfigInterface::class)->get('server', []);
        if (!$serverConfig) {
            throw new InvalidArgumentException('At least one server should be defined.');
        }
        $serverFactory->configure($serverConfig);
        Coroutine::set(['hook_flags' => swoole_hook_flags()]);
        $this->line('hyperf server start success.', 'info');
        $serverFactory->start();
    }

    private function stopServer()
    {
        $pidFile = BASE_PATH . '/runtime/hyperf.pid';
        $pid = file_exists($pidFile) ? intval(file_get_contents($pidFile)) : false;
        if ($pid && Process::kill($pid, SIG_DFL)) {
            if (!Process::kill($pid, SIGTERM)) {
                $this->line('hyperf server stop error.', 'error');
                die();
            }
            while (Process::kill($pid, SIG_DFL)) {
                sleep(1);
            }
            $this->line('hyperf server stop success.', 'info');
        }else{
            $match = env('APP_NAME') . '.Master';
//            $match = 'php bin/hyperf.php up'; // -w
            $command = "ps -ef | grep '$match' | grep -v grep | awk '{print $2}' | xargs kill -9 2>&1";
            // 找不到pid，强杀进程
            exec($command);
            if($pidFile) exec('rm -rf ' . $pidFile);
            $this->line('hyperf server stop kill success.', 'info');
        }
    }

    private function monitorDirs(bool $recursive = false)
    {
        $dirs[] = BASE_PATH . '/app';
        $dirs[] = BASE_PATH . '/config';

        if ($recursive) {
            foreach ($dirs as $dir) {
                $dirIterator = new \RecursiveDirectoryIterator($dir);
                $iterator = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::SELF_FIRST);
                /** @var \SplFileInfo $file */
                foreach ($iterator as $file) {
                    if ($file->isDir() && $file->getFilename() != '.' && $file->getFilename() != '..') {
                        $dirs[] = $file->getPathname();
                    }
                }
            }
        }

        return $dirs;
    }

    private function monitorFiles()
    {
        $files[] = BASE_PATH . '/.env';
        return $files;
    }




    private function clearRuntimeContainer()
    {
        exec('rm -rf ' . BASE_PATH . '/runtime/container');
    }
    private function checkEnvironment(OutputInterface $output)
    {
        if (! extension_loaded('swoole') || ! Composer::hasPackage('hyperf/polyfill-coroutine')) {
            return;
        }
        /**
         * swoole.use_shortname = true       => string(1) "1"     => enabled
         * swoole.use_shortname = "true"     => string(1) "1"     => enabled
         * swoole.use_shortname = on         => string(1) "1"     => enabled
         * swoole.use_shortname = On         => string(1) "1"     => enabled
         * swoole.use_shortname = "On"       => string(2) "On"    => enabled
         * swoole.use_shortname = "on"       => string(2) "on"    => enabled
         * swoole.use_shortname = 1          => string(1) "1"     => enabled
         * swoole.use_shortname = "1"        => string(1) "1"     => enabled
         * swoole.use_shortname = 2          => string(1) "1"     => enabled
         * swoole.use_shortname = false      => string(0) ""      => disabled
         * swoole.use_shortname = "false"    => string(5) "false" => disabled
         * swoole.use_shortname = off        => string(0) ""      => disabled
         * swoole.use_shortname = Off        => string(0) ""      => disabled
         * swoole.use_shortname = "off"      => string(3) "off"   => disabled
         * swoole.use_shortname = "Off"      => string(3) "Off"   => disabled
         * swoole.use_shortname = 0          => string(1) "0"     => disabled
         * swoole.use_shortname = "0"        => string(1) "0"     => disabled
         * swoole.use_shortname = 00         => string(2) "00"    => disabled
         * swoole.use_shortname = "00"       => string(2) "00"    => disabled
         * swoole.use_shortname = ""         => string(0) ""      => disabled
         * swoole.use_shortname = " "        => string(1) " "     => disabled.
         */
        $useShortname = ini_get_all('swoole')['swoole.use_shortname']['local_value'];
        $useShortname = strtolower(trim(str_replace('0', '', $useShortname)));
        if (! in_array($useShortname, ['', 'off', 'false'], true)) {
            $output->writeln("<error>ERROR</error> Swoole short function names must be disabled before the server starts, please set swoole.use_shortname='Off' in your php.ini.");
            exit(SIGTERM);
        }
    }




    private function watchServer()
    {
        $this->line('start new hyperf server.', 'info');
        $pid = $this->startProcess();
        while ($pid > 0) {
            $this->watch();
            $this->stopProcess($pid);
            $pid = $this->startProcess();
            sleep(1);
        }
    }


    private function watch()
    {
        if (!extension_loaded('inotify')) {
            $this->line('php inotify extension not found.', 'error');
            die();
        }
        $fd = inotify_init();
        stream_set_blocking($fd, false);
        $dirs = $this->monitorDirs(true);
        foreach ($dirs as $dir) {
            $this->line('dir):'.$dir, 'info');
            inotify_add_watch($fd, $dir, IN_CREATE | IN_MODIFY | IN_DELETE | IN_MOVE);
        }
        $files = $this->monitorFiles();
        foreach ($files as $file) {
            $this->line('file:'.$file, 'info');
            inotify_add_watch($fd, $file, IN_CREATE | IN_MODIFY | IN_DELETE | IN_MOVE);
        }
//        $ret = inotify_read($fd); //虚拟机挂在目录不能监听
        fclose($fd);
    }
    private function startProcess()
    {
        $this->clearRuntimeContainer();
        $process = new Process(function (Process $process) {
            $args = [BASE_PATH . '/bin/hyperf.php', 'start'];
            $process->exec('/www/server/php/82/bin/php', $args);
        });
        return $process->start();
    }

    private function stopProcess(int $pid): bool
    {
        $this->line('hyperf watch server stop. pid:'. $pid, 'info');
        $timeout = 15;
        $startTime = time();
        while (true) {
            $ret = Process::wait(false);
            if ($ret && $ret['pid'] == $pid) {
                return true;
            }
            if (!Process::kill($pid, SIG_DFL)) {
                return true;
            }
            if ((time() - $startTime) >= $timeout) {
                $this->line('hyperf watch server stop timeout:', 'error');
                return false;
            }
            Process::kill($pid, SIGTERM);
            sleep(1);
            $this->line('hyperf watch server success:', 'info');
        }
        return false;
    }

}
