<?php
/**
 * WP Redis SSE, Pub Sub
 *
 * @package WpRedisSse
 */

namespace WpRedisSse;

use Redis;

/**
 * Pub Sub
 */
class PubSub {

	/**
	 * Redis client
	 *
	 * @var Redis
	 */
	protected static $redis;

	function __construct() {
		static::$redis = utils\get_client();
	}

	public static function publish( string $key, array|string $value ) {
		$redis = static::$redis ?? utils\get_client();
		$redis->publish(
			'wrs-channel',
			EventMessage::create( $key, $value )->encode()
		);
	}

	public static function subscribe( array $channels, callable $callback, Redis $redis_client ) {
		$redis = $redis_client ?? static::$redis ?? utils\get_client();
		$redis->subscribe( $channels, $callback );
	}
}
