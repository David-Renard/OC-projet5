<?php

namespace App\Service;

use App\Service\Http\Request;
use App\Service\Http\Session\Session;

class Token
{
    public function __construct(private Session $session)
    {
    }

    public function setToken(): void
    {
        $this->session->set('token', md5(bin2hex(openssl_random_pseudo_bytes(12))));
    }

    public function getToken(): string
    {
        return $this->session->get('token');
    }

    public function verifyToken(Request $request): bool
    {
        if ($request->getMethod() === 'POST') {
            if ($this->getToken() === $request->request()->get('token')) {
                return true;
            }
        } elseif ($request->getMethod() === 'GET') {
            if ($this->getToken() === $request->query()->get('token')) {
                return true;
            }
        }
        return false;
    }
}