<?php

namespace App\Kernel\Http;

class Request
{
    public function __construct(
        private array $get,
        private array $post,
        private array $server,
        private array $files,
        private array $cookies)
    {
    }

    public static function createFromGlobals():static
    {
        return new static($_GET, $_POST, $_SERVER, $_FILES, $_COOKIE);
    }
    public function uri():string
    {
        return strtok($this->server['REQUEST_URI'], '?');
    }

    public function method():string
    {
        return $this->server['REQUEST_METHOD'];
    }
}