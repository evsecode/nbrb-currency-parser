<?php

namespace App\Kernel\Session;

interface SessionInterface
{
    public function set(string $key, $value): void;

    public function get(string $key, $default = null): mixed;

    public function getFlash(string $key, $default = null): mixed;

    public function remove(string $key): void;

    public function has(string $key): bool;

    public function destroy(): void;
}