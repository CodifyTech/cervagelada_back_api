<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Alert Log Channel
    |--------------------------------------------------------------------------
    | Channel for alert notifications. Set to 'slack' for Slack alerts.
    | Falls back to the default logging channel.
    */
    'log_channel' => env('ALERTS_LOG_CHANNEL', env('LOG_CHANNEL', 'stack')),

    /*
    |--------------------------------------------------------------------------
    | Brute Force Detection
    |--------------------------------------------------------------------------
    */
    'brute_force' => [
        'threshold' => (int) env('ALERTS_BRUTE_FORCE_THRESHOLD', 5),
        'window_minutes' => (int) env('ALERTS_BRUTE_FORCE_WINDOW', 10),
        'cooldown_minutes' => (int) env('ALERTS_BRUTE_FORCE_COOLDOWN', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stalled Orders
    |--------------------------------------------------------------------------
    */
    'stalled_orders' => [
        'hours_threshold' => (int) env('ALERTS_STALLED_HOURS', 2),
        'cooldown_minutes' => (int) env('ALERTS_STALLED_COOLDOWN', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Refused Payments
    |--------------------------------------------------------------------------
    */
    'refused_payments' => [
        'window_minutes' => (int) env('ALERTS_REFUSED_WINDOW', 5),
        'cooldown_minutes' => (int) env('ALERTS_REFUSED_COOLDOWN', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Failures (Asaas)
    |--------------------------------------------------------------------------
    */
    'webhook_failures' => [
        'threshold' => (int) env('ALERTS_WEBHOOK_THRESHOLD', 3),
        'window_minutes' => (int) env('ALERTS_WEBHOOK_WINDOW', 5),
        'cooldown_minutes' => (int) env('ALERTS_WEBHOOK_COOLDOWN', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Errors (500)
    |--------------------------------------------------------------------------
    */
    'server_errors' => [
        'threshold' => (int) env('ALERTS_500_THRESHOLD', 5),
        'window_minutes' => (int) env('ALERTS_500_WINDOW', 5),
        'cooldown_minutes' => (int) env('ALERTS_500_COOLDOWN', 30),
    ],

];
