<?php

namespace App\Service\Http\Session;

use App\Service\Http\Request;

class Token
{
    public function __construct(private Session $session)
    {
    }

    public function setToken(): ?string
    {
        if (empty($this->session->get('token')))
        {
            $this->session->set('token',md5(bin2hex(openssl_random_pseudo_bytes(12))));
        }
        return $this->session->get('token');
    }

    public function verifyToken(Request $request): bool
    {
        if ($request->getMethod() === 'POST')
        {
            if ($this->session->get('token') === $request->request()->get('token'))
            {
                return true;
            }
        }
        elseif ($request->getMethod() === 'GET')
        {
            if ($this->session->get('token') === $request->query()->get('token'))
            {
                return true;
            }
        }
        return false;
    }
}