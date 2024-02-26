<?php

/**
 * This script is intentionally agnostic of WordPress. This avoids the unnecessary 
 * overhead of loading WordPress when it's not needed.
 */

use WpRedisSse\utils;

require_once __DIR__ . '/includes/utils.php';

set_time_limit(0);

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
header('X-Accel-Buffering: no');
ob_implicit_flush(true);

$redis = utils\get_client();
$loop = $redis->pubSubLoop(['subscribe' => 'site-option-update']);

/** @var Predis\PubSub\Consumer\Message $message */
foreach ($loop as $message) {
    // Immediately handle message if present
    if ($message->kind === 'message' && $message->channel === 'site-option-update') {
        echo "event: siteOptionUpdate\n";
        echo "data: {$message->payload}\n\n";
    } elseif ($message->kind === 'subscribe') {
        echo "event: subscribe\n";
        echo "data: Subscribed to {$message->channel}\n\n";
    }

    // Flush the output buffer and send echoed messages to the browser
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    flush();

    // Break the loop if the connection is closed by the client
    if (connection_status() != CONNECTION_NORMAL) {
        break;
    }

    // Sleep for a bit to prevent the script from consuming too much CPU
    sleep(1);
}

$loop->unsubscribe();
$loop->stop();
