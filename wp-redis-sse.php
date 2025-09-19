<?php

namespace WpRedisSse;

/**
 * Plugin Name:         WP Redis SSE
 * Requires Plugins:    wp-redis
 * Plugin URI:          https://github.com/jaredrethman/wp-redis-sse
 * Description:         A WordPress plugin for Server-Sent-Events using Redis publish-subscribe
 * Author:              Jared Rethman
 * Author URI:          https://jaredrethman.com
 * Text Domain:         wp-redis-sse
 * Domain Path:         /languages
 * Version:             0.0.1
 * Required:            6.5.0
 * Required PHP:        8.0.0
 * Requires Plugins:    wp-redis
 * Network:             true
 * GitHub Plugin URI:   https://github.com/jaredrethman/wp-redis-sse
 *
 * @package             WpRedisSse
 */

// Compile time constants
const DIR = __DIR__;
const VER = '0.0.1';

// Runtime constants
define( __NAMESPACE__ . '\\URL', plugins_url( '', __FILE__ ) );
define( __NAMESPACE__ . '\\EVENT_SOURCE_FILE', URL . '/sse.php' );

require_once DIR . '/includes/constants.php';

/**
 * When installing/using wp-redis, setting `$redis_server` in wp-config.php
 * if required. If it doesn't exist, wp-redis plugin isn't active and/or installed.
 */
if ( empty( $redis_server ) ) {
	return;
}

require_once DIR . '/includes/includes.php';
