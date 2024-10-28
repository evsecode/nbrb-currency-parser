<?php

namespace App\Kernel\Cache;

interface CacheInterface
{
    public function get(string $key): ?int;

    public function set(string $key, int $value, int $ttl): void;
}
