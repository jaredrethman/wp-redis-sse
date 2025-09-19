<?php
/**
 * Lightweight server-sent events (SSE) implementation using Redis Pub/Sub
 */

use WpRedisSse\{
	PubSub
};

use function WpRedisSse\utils\{
	format_sse,
	get_client
};

require_once __DIR__ . '/includes/utils.php';
require_once __DIR__ . '/includes/constants.php';
require_once __DIR__ . '/includes/class-pub-sub.php';

$redis_client = get_client( $redis_server );

set_time_limit( 0 );

header( 'Content-Type: text/event-stream' );
header( 'Cache-Control: no-cache' );
header( 'Connection: keep-alive' );
header( 'X-Accel-Buffering: no' );
ob_implicit_flush( true );

try {
	// Inform the client of a successful subscription.
	echo format_sse(
		array(
			'event' => 'subscribe',
			'data'  => 'subscribed',
		)
	);
	ob_flush();
	flush();

	PubSub::subscribe(
		array( 'wrs-channel' ),
		function ( $redis, string $channel, string $message ) {
			if ( $channel === 'wrs-channel' ) {
				echo format_sse(
					array(
						'data'  => $message,
						'event' => 'wpRedisSse',
					)
				);
				ob_flush();
				flush();
			}
		},
		$redis_client
	);
} catch ( Throwable $th ) {
	// Log or handle the error as appropriate.
	error_log( 'Error occurred in Redis subscription: ' . $th->getMessage() );

	// Inform the client that an error has occurred.
	echo format_sse(
		array(
			'data'  => json_encode( $th->getMessage() ),
			'event' => 'error',
		)
	);
}

$redis_client->close();
