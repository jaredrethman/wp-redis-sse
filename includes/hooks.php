<?php

/**
 * WP Redis SSE, wp hooks (actions/filters)
 *
 * @package WpRedisSse
 */

namespace WpRedisSse\hooks;

use Throwable;
use WpRedisSse\{
	PubSub,
};
use const WpRedisSse\{
	URL,
	EVENT_SOURCE_FILE,
};

/**
 * When an option in options table is updated, publish
 * those changes using Predis Pub Sub
 *
 * @example
 */
add_action(
	'updated_option',
	function ( $option_name, $old_value, $new_value ) {
		try {
			PubSub::publish(
				'option',
				array(
					'blog_id'    => get_current_blog_id(),
					$option_name => $new_value,
				)
			);
		} catch ( Throwable $th ) {
		}
	},
	10,
	3
);

/**
 * When a post is updated, publish
 * those changes using Predis Pub Sub
 *
 * @example
 */
add_action(
	'save_post',
	function ( $post_id, $post, $update ) {
		if ( $post->post_type !== 'post' ) {
			return;
		}
		try {
			PubSub::publish(
				'post',
				array(
					'blog_id' => get_current_blog_id(),
					'post_id' => $post_id,
					'post'    => $post,
					'update'  => $update,
				)
			);
		} catch ( Throwable $th ) {
		}
	},
	10,
	3
);

/**
 * Add script to utilise EventSource on our SSE file
 */
add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_script( 'wp-redis-sse', URL . '/src/index.js', array() );
		wp_localize_script(
			'wp-redis-sse',
			'wpRedisSse',
			array(
				'eventSource' => URL . '/sse.php',
			)
		);
	}
);
