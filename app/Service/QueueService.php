<?php
declare(strict_types=1);

namespace App\Service;

use App\Amqp\Producer\QueueProducer;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Context\ApplicationContext;


class QueueService
{
    public static function __callStatic($func, $arguments)
    {
        $params = $arguments[0] ?? null;
        $delay = $arguments[1] ?? 0;
        $pool = $arguments[2] ?? 'default';
        $container = ApplicationContext::getContainer();
        return $container->get(DriverFactory::class)->get($pool)->push($params, $delay);
    }


}