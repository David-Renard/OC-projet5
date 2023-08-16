<?php
declare(strict_types=1);
namespace App\Service\FormValidator;

use App\Service\Http\Request;
use App\Service\Http\Session\Session;
class ContactFormValidator
{
    private ?array $contactArray=[];
    public function __construct(private Request $request, private Session $session)
    {
        $this->contactArray = $this->request->request()->all();
    }
    public function isInputValid(string $pattern, string $value):mixed
    {
        if ($this->contactArray === null)
        {
            return false;
        }

        if (!preg_match($pattern, $value))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    public function isEmailValid(string $value): bool
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        else
        {
            return true;
        }
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
    public function isRgpdChecked(mixed $value):bool
    {
        if ($value === 'on')
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}