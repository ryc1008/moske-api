<?php
declare(strict_types=1);

namespace App\Listener;

use Hyperf\Crontab\Event\FailToExecute;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class FailToExecuteCrontabPostListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            FailToExecute::class
        ];
    }

    public function process(object $event): void
    {
        logger_write('crontab_listener', [$event->crontab->getName(). 'is fail: '.$event->throwable->getMessage(), strtolower('/App/Listener/FailToExecuteCrontabPostListener'), 24], 'error', 'crontab');
    }
}
