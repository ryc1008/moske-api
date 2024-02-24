<?php
declare(strict_types=1);

namespace App\Service;

use Hyperf\Context\ApplicationContext;
use Hyperf\Logger\LoggerFactory;

class LoggerService
{
    public static function __callStatic($func, $arguments)
    {
        $container = ApplicationContext::getContainer();
        $name = $arguments[2] ?? 'log';
        $message = $arguments[0];
        $data = is_array($arguments[1]) ? $arguments[1] : [$arguments[1]];
        $container->get(LoggerFactory::class)->get($name)->$func($message, $data);
    }
}