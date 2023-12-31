<?php

use Monolog\Handler\NullHandler;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/default.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'sql-logger' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sql.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'slow-sql-logger' => [
            'driver' => 'daily',
            'path' => storage_path('logs/slow-sql.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        \App\Constant::LOG_CHANNEL_RONIN => [
            'driver' => 'daily',
            'path' => storage_path('logs/ronin.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        \App\Constant::LOG_CHANNEL_AXIE => [
            'driver' => 'daily',
            'path' => storage_path('logs/axie.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/emergency.log'),
        ],
    ],
];
