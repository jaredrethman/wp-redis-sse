<?php

/**
 * WP Redis SSE, wp hooks (actions/filters)
 * 
 * @package WpRedisSse
 */

namespace WpRedisSse\hooks;

use WpRedisSse\{
    utils,
    EventMessage
};

/**
 * When an option in options table is updated, publish
 * those changes using Predis Pub Sub
 */
add_action('updated_option', function ($option_name, $old_value, $new_value) {
    try {
        $redis = utils\get_client();
        $blog_id = get_current_blog_id();
        $redis->publish(
            'wrs-options-channel',
            EventMessage::create('option', [$option_name => $new_value, 'blog_id' => $blog_id])->encode()
        );
    } catch (\Throwable $th) {
    }
}, 10, 3);

/**
 * Add script to utilise EventSource on our SSE file
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('wp-redis-sse', WRS_URL . '/src/index.js', []);
    wp_localize_script('wp-redis-sse', 'wpRedisSse', [
        'eventSource' => WRS_URL . '/sse.php'
    ]);
});
