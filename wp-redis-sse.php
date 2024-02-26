<?php

/**
 * Plugin Name:         WordPress Redis SSE
 * Requires Plugins:    wp-redis
 * Plugin URI:          https://github.com/jaredrethman/wp-redis-sse
 * Description:         A WordPress plugin for Server-Sent-Events using Redis pubSub
 * Author:              Jared Rethman
 * Author URI:          https://github.com/jaredrethman
 * Text Domain:         wp-redis-sse
 * Domain Path:         /languages
 * Version:             0.0.1
 *
 * @package             WpRedisSse
 */

const WRS_DIR = __DIR__;
const WRS_VER = '0.0.1';
define('WRS_URL', plugins_url('', __FILE__));

/**
 * When installing/using wp-redis, setting `$redis_server` in wp-config.php
 * if required. If it doesn't exist, wp-redis plugin isn't active and/or installed.
 */
if (empty($redis_server)) {
    return;
}

require_once WRS_DIR . '/includes/includes.php';
