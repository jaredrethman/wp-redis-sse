<?php

/**
 * WP Redis SSE - Utility functions and helpers
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
    static $client;
    try {
        if (null === $client) {
            $connect_args = [
                'host'           => $connect_params['host'] ?? '127.0.0.1',
                'port'           => $connect_params['port'] ?? 6379,
                'connectTimeout' => $connect_params['timeout'] ?? 60,
            ];
            if (isset($connect_params['auth'])) {
                $connect_args['auth'] = $connect_params['auth'];
            }
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
