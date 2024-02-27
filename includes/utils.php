<?php
/**
 * WP Redis SSE - Utility functions and helpers
 * 
 * @package WpRedisSse
 */

namespace WpRedisSse\utils;

use Redis;
use Exception;
use Throwable;

/**
 * Get Redis client
 * @return Redis Redis Client
 */
function get_client($connect_params = [])
{
    if (!class_exists('\\Redis')) {
        throw new Exception(__FUNCTION__ . '\\Error: PhpRedis extension is required to use this plugin.');
    }
    static $client;
    try {
        if (null === $client) {
            // wp-redis wp-config.php global
            global $redis_server;
            // Attempt to resolve using env variables i.e. $_SERVER
            $connect_args = [
                'host'           => $_SERVER['CACHE_HOST'] ?? $redis_server['host'] ?? $connect_params['host'] ?? '127.0.0.1',
                'port'           => $_SERVER['CACHE_PORT'] ?? $redis_server['port'] ?? $connect_params['port'] ?? 6379,
                'auth'           => $_SERVER['CACHE_PASSWORD'] ?? $redis_server['auth'] ?? $connect_params['auth'] ?? '',
                'connectTimeout' => $connect_params['timeout'] ?? 60, // sec
                'retryInterval'  => $connect_params['retry-interval'] ?? 100, // ms
            ];
            $client = new Redis($connect_args);
        }
    } catch (Throwable $th) {
        throw new Exception(__FUNCTION__ . '\\Error:' . $th->getMessage());
    }
    return $client;
}

/**
 * Format, event streaming text data
 * @param array $message Message array 
 * @return string 
 */
function format_sse($args)
{
    if (!isset($args['event']) || !isset($args['data'])) {
        return '';
    }

    $sseData = "";

    foreach ($args as $key => $value) {
        $sseData .= "{$key}: {$value}\n";
    }

    $sseData .= "\n";

    return $sseData;
}
