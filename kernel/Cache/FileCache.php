<?php

namespace App\Kernel\Cache;

class FileCache implements CacheInterface
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function get(string $key): ?int
    {
        $cacheFile = $this->getCacheFilePath($key);
        if (! file_exists($cacheFile)) {
            return null;
        }

        return (int) file_get_contents($cacheFile);
    }

    public function set(string $key, int $value, int $ttl): void
    {
        $cacheFile = $this->getCacheFilePath($key);
        $result = file_put_contents($cacheFile, $value);

        if ($result === false) {
            error_log("Ошибка записи в кеш-файл: $cacheFile");
        } else {
            error_log("Кеш установлен для ключа $key. Значение: $value");
            touch($cacheFile, strtotime(date('Y-m-d')) + $ttl);
        }
    }

    private function getCacheFilePath(string $key): string
    {
        return $this->cacheDir.'/'.md5($key).'.cache';
    }
}
