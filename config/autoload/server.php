<?php

declare(strict_types=1);

use Hyperf\Server\Event;
use Hyperf\Server\Server;
use Swoole\Constant;

return [
    'mode' => SWOOLE_PROCESS,
//    'type' => Hyperf\Server\CoroutineServer::class,//使用协程为单进程http访问
    'servers' => [
        [
            'name' => 'http',
            'type' => Server::SERVER_HTTP,
            'host' => '0.0.0.0',
            'port' => 9501,
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                Event::ON_REQUEST => [Hyperf\HttpServer\Server::class, 'onRequest'],
            ],
            'options' => [
                // Whether to enable request lifecycle event
                'enable_request_lifecycle' => false,
            ],
        ],
    ],
    'settings' => [
        Constant::OPTION_ENABLE_COROUTINE => true, // 开启内置协程
        Constant::OPTION_WORKER_NUM => swoole_cpu_num(),// 设置启动的 Worker 进程数
        Constant::OPTION_PID_FILE => BASE_PATH . '/runtime/hyperf.pid',// master 进程的 PID
        Constant::OPTION_OPEN_TCP_NODELAY => true,// TCP 连接发送数据时会关闭 Nagle 合并算法，立即发往客户端连接
        Constant::OPTION_MAX_COROUTINE => 100000,// 设置当前工作进程最大协程数量
        Constant::OPTION_OPEN_HTTP2_PROTOCOL => true,// 启用 HTTP2 协议解析
        Constant::OPTION_MAX_REQUEST => 100000,// 设置 worker 进程的最大任务数
        Constant::OPTION_SOCKET_BUFFER_SIZE => 2 * 1024 * 1024,// 配置客户端连接的缓存区长度
        Constant::OPTION_BUFFER_OUTPUT_SIZE => 2 * 1024 * 1024,
        Constant::OPTION_DOCUMENT_ROOT => BASE_PATH . '/public',
        Constant::OPTION_ENABLE_STATIC_HANDLER => true,
//        Constant::OPTION_DAEMONIZE => 1, //将服务设置成守护进程模式,热启动不能开启
    ],
    'callbacks' => [
        Event::ON_WORKER_START => [Hyperf\Framework\Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
        Event::ON_PIPE_MESSAGE => [Hyperf\Framework\Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
        Event::ON_WORKER_EXIT => [Hyperf\Framework\Bootstrap\WorkerExitCallback::class, 'onWorkerExit'],
    ],
];
