<?php
declare(strict_types=1);

use Hyperf\Crontab\Crontab;
use App\Task\LiveCrontab;
use App\Task\LoggerCrontab;

return [
    // 是否开启定时任务
    'enable' => true,
    'crontab' => [
        (new Crontab())->setName('live-reset')->setRule('0 0 * * *')->setCallback([LiveCrontab::class, 'reset']),
        (new Crontab())->setName('live-work')->setRule('* * * * *')->setCallback([LiveCrontab::class, 'work']),
        (new Crontab())->setName('logger-clear')->setRule('0 0 * * *')->setCallback([LoggerCrontab::class, 'clear']),
    ],
];