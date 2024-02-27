<?php

/**
 * This script is intentionally agnostic of WordPress. This avoids the unnecessary 
 * overhead of loading WordPress when it's not needed.
 */

use WpRedisSse\utils;

require_once __DIR__ . '/includes/utils.php';

$redis_client = utils\get_client([
    'host' => 'redis',
    'auth' => 'root'
]);
set_time_limit(0);

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
header('X-Accel-Buffering: no');
ob_implicit_flush(true);

try {
    // Inform the client of a successful subscription.
    echo utils\format_sse(['event' => 'subscribe', 'data' => 'subscribed']);

    // Start the subscription
    $redis_client->subscribe(['wrs-options-channel'], function ($redis, string $channel, string $message) {
        if ($channel === 'wrs-options-channel') {
            echo utils\format_sse([
                'data'  => $message,
                'event' => 'wpRedisSse',
            ]);
            ob_flush();
            flush();
        }
    });
} catch (\Throwable $th) {
    // Log or handle the error as appropriate.
    error_log("Error occurred in Redis subscription: " . $th->getMessage());

    // Inform the client that an error has occurred.
    echo utils\format_sse([
        'data' => json_encode($th->getMessage()),
        'event' => 'error',
    ]);
}

$redis_client->close();
