<?php

namespace WpRedisSse;

$redis_server = $redis_server ?? array(
	'host' => 'redis',
	'port' => 6379,
	'auth' => 'root',
);
