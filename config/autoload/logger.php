<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'default' => [
//        'handler' => [
//            'class' => Monolog\Handler\StreamHandler::class,
//            'constructor' => [
//                'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
//                'level' => Monolog\LoggerService::DEBUG,
//            ],
//        ],
        /*
         * 日志文件按日期轮转
         * 修改 config/autoload/logger.php 配置文件，
         * 将 Handler 改为 Monolog\Handler\RotatingFileHandler::class，并将 stream 字段改为 filename 即可。
         */
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/hyperf.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
];
