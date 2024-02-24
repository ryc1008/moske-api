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

use function Hyperf\Support\env;
use \Hyperf\Database\Commands\ModelOption;

return [
    'default' => [
        'driver' => env('DB_DRIVER', 'mysql'),
        'host' => env('DB_HOST', 'localhost'),
        'database' => env('DB_DATABASE', 'hyperf'),
        'port' => env('DB_PORT', 3306),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8'),
        'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
        'prefix' => env('DB_PREFIX', ''),
        'pool' => [
            'min_connections' => 5,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float)env('DB_MAX_IDLE_TIME', 60),
        ],
        'commands' => [
            'gen:model' => [
                'path' => 'app/Model',
                'force_casts' => true,
                'inheritance' => 'Base',
                'uses' => '',
                'refresh_fillable' => true,
                'with_comments' => true,
                'property_case' => ModelOption::PROPERTY_SNAKE_CASE,
            ],
        ],
    ],
//    'alibaba' => [
//        'driver' => 'mysql',
//        'host' => '107.148.93.38',
//        'database' => 'aiaiba110_com',
//        'port' => 3306,
//        'username' => 'aiaiba110_com',
//        'password' => 'WpbR2hmCW3MHxF37',
//        'charset' => 'utf8mb4',
//        'collation' => 'utf8mb4_general_ci',
//        'prefix' => 'mac_',
//        'pool' => [
//            'min_connections' => 10,
//            'max_connections' => 20,
//            'connect_timeout' => 10.0,
//            'wait_timeout' => 3.0,
//            'heartbeat' => -1,
//            'max_idle_time' => (float)env('DB_MAX_IDLE_TIME', 60),
//        ],
//        'commands' => [
//            'gen:model' => [
//                'path' => 'app/Model',
//                'force_casts' => true,
//                'inheritance' => 'Base',
//                'uses' => '',
//                'refresh_fillable' => true,
//                'with_comments' => true,
//                'property_case' => ModelOption::PROPERTY_SNAKE_CASE,
//            ],
//        ],
//    ],
];
