<?php
declare(strict_types=1);
namespace App\Service\FormValidator;

use App\Service\Http\Request;
use App\Service\Http\Session\Session;
class CommentFormValidator
{
    private ?array $contactArray=[];
    public function __construct(private Request $request, private Session $session)
    {
        $this->contactArray = $this->request->request()->all();
    }

    public function isTextareaValid(mixed $value):bool
    {
        if ($value !== NULL && $value!=="")
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function isLogged():bool
    {
        if ($this->session->get('user'))
        {
            if ($this->session->get('user')->getId())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}