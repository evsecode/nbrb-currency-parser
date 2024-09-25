<?php

namespace App\Kernel\Http;

use App\Kernel\Validator\ValidatorInterface;

interface RequestInterface
{
    public function uri(): string;

    public function method(): string;

    public function input(string $key, $default = null);

    public function setValidator(ValidatorInterface $validator);

    public function validate(array $rules);

    public function errors();
}