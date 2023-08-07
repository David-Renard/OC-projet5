<?php

declare(strict_types=1);

namespace App\Service\Http;

class ParametersBag
{
    protected array $parameters;
    public function __construct(array &$parameters)
    {
        $this->parameters=&$parameters;
    }
    public function all():?array
    {
        return $this->parameters;
    }
    public function has(string $key):bool
    {
        return isset($this->parameters[$key]);
    }
    public function get(string $key):mixed
    {
        return $this->has($key) ? $this->parameters[$key] : null;
    }
    public function set(string $key,mixed $value):void
    {
        $this->parameters[$key]=$value;
    }
}