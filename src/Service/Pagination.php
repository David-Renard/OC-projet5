<?php

namespace App\Service;

use App\Service\Http\Request;

class Pagination
{
    public function __construct(private Request $request, private array $arrayToCount)
    {
    }

    public function nbPerPage(): int
    {
        $return = 5;
        if ($this->request->query()->has('per'))
        {
            $perPage = intval($this->request->query()->get('per'));
            if ($perPage === 3 || $perPage === 5 || $perPage === 10)
            {
                $return = $perPage;
            }
        }
        return $return;
    }

    public function nbPages(): int
    {
        return ceil(count($this->arrayToCount)/$this->nbPerPage());
    }

    public function currentPage(): int
    {
        $return = 1;
        if ($this->request->query()->has('page'))
        {
            $currentPage = intval($this->request->query()->get('page'));
            if ($currentPage > 0 && $currentPage < $this->nbPages())
            {
                $return = $currentPage;
            }
            elseif ($currentPage >= $this->nbPages())
            {
                $return = $this->nbPages();
            }
        }
        return $return;
    }
}