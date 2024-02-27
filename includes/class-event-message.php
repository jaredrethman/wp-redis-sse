<?php

namespace WpRedisSse;

use JsonException;

class EventMessage
{
    private string $key;
    private array|string $value;

    private string $encoded_value = '';

    public function __construct(string $key, array|string $value)
    {
        $this->key   = $key;
        $this->value = $value;

        return $this;
    }

    final public static function create(string $key, array|string $value)
    {
        return new EventMessage($key, $value);
    }

    public function encode(): string
    {
        try {
            $this->encoded_value = json_encode([
                'key'   => $this->key,
                'value' => $this->value,
            ], JSON_THROW_ON_ERROR);
            return $this->encoded_value;
        } catch (JsonException $exception) {
            return '';
        }
    }

    public function decode($string): string|array
    {
        try {
            return json_decode($string ?? $this->encoded_value);
        } catch (JsonException $exception) {
            return [];
        }
    }
}
