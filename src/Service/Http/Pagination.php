<?php

namespace App\Service\Http;

use App\Service\Http\Request;

class Pagination
{
    public function __construct(private Request $request, private array $arrayToCount)
    {
    }

    public function nbPerPage(): int
    {
        if ($this->request->getMethod() === 'GET')
        {
            if ($this->request->query()->has('per'))
            {
                $perPage = intval($this->request->query()->get('per'));
                if ($perPage >= 0)
                {
                    return $perPage;
                }
                else
                {
                    return 5;
                }
            }
            return 5;
        }
    }

    public function nbPages(): int
    {
        return ceil(count($this->arrayToCount)/$this->nbPerPage());
    }

    public function currentPage(): int
    {
        if ($this->request->getMethod() === 'GET')
        {
            if ($this->request->query()->has('page'))
            {
                $currentPage = intval($this->request->query()->get('page'));
                if ($currentPage > 0 && $currentPage < $this->nbPages())
                {
                    return $currentPage;
                }
                elseif ($currentPage <= 0)
                {
                    return 1;
                }
                else
                {
                    return $this->nbPages();
                }
            }
            return 1;
        }
    }
}