<?php

namespace App\Service;

use Symfony\Component\Dotenv\Dotenv;

class Environment
{
    public function __construct(public Dotenv $dotenv)
    {
        $this->dotenv->load("..\.env");
    }

    public function getEnv(string $key): string
    {
        return $_ENV[$key];
    }
}