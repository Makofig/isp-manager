<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Reverb Server
    |--------------------------------------------------------------------------
    |
    | This option controls the default server used by Reverb to handle
    | incoming messages from clients. This feature is only available
    | when using Reverb to handle WebSocket connections.
    |
    */

    'default' => env('REVERB_SERVER', 'reverb'),

    /*
    |--------------------------------------------------------------------------
    | Reverb Servers
    |--------------------------------------------------------------------------
    |
    | Here you may define details for each of the servers that Reverb
    | manages. Each server is independently configured and may run on
    | a different host or port.
    |
    */

    'servers' => [

        'reverb' => [
            'host' => env('REVERB_SERVER_HOST', '0.0.0.0'),
            'port' => env('REVERB_SERVER_PORT', 8080),
            'hostname' => env('REVERB_HOST'),
            'options' => [
                'tls' => [],
            ],
            'scaling' => [
                'enabled' => env('REVERB_SCALING_ENABLED', false),
                'channel' => env('REVERB_SCALING_CHANNEL', 'reverb'),
                'server' => [
                    'url' => env('VITE_REDIS_HOST'),
                    'host' => env('REVERB_SERVER_HOST', '127.0.0.1'),
                    'port' => env('VITE_REDIS_PORT', 6379),
                    'username' => env('VITE_REDIS_USERNAME'),
                    'password' => env('VITE_REDIS_PASSWORD'),
                    'database' => env('VITE_REDIS_DB', '0'),
                ],
            ],
            'pulse_ingest_interval' => env('REVERB_PULSE_INGEST_INTERVAL', 15),
            'telescope_ingest_interval' => env('REVERB_TELESCOPE_INGEST_INTERVAL', 15),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Reverb Applications
    |--------------------------------------------------------------------------
    |
    | This option determines which applications are allowed to consume
    | the messages transmitted via the WebSocket connections managed by
    | Reverb.
    |
    */

    'apps' => [

        'provider' => 'config',

        'allowed_origins' => ['*'],

        'max_message_size_in_kb' => env('REVERB_MAX_MESSAGE_SIZE_IN_KB', 100),

        'options' => [
            'host' => env('REVERB_HOST'),
            'port' => env('REVERB_PORT', 443),
            'scheme' => env('REVERB_SCHEME', 'https'),
            'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
        ],

    ],

];
