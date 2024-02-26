<?php
/**
 * WP Redis SSE - Utility functions and helpers
 */

namespace WpRedisSse\utils;
use Predis;

require_once __DIR__ . '/../vendor/autoload.php';

const PORT = 6379;
const HOST = 'redis';

/**
 * Get Redis client
 * @return Client Predis Client
 */
function get_client()
{
    static $client;
    $client ??= new Predis\Client([
        'scheme' => 'tcp',
        'host'   => HOST,
        'port'   => PORT,
    ]);

    return $client;
}