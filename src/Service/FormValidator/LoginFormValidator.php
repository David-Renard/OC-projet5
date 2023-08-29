<?php
declare(strict_types=1);
namespace App\Service\FormValidator;

use App\Model\Entity\User;
use App\Service\Http\Request;
use App\Model\Repository\UserRepository;
use App\Service\Http\Session\Session;
class LoginFormValidator
{
    private ?array $infoUser=[];
    public function __construct(private Request $request, private UserRepository $userRepository, private Session $session)
    {
        $this->infoUser = $this->request->request()->all();
    }

    public function isValid():bool
    {
        if ($this->infoUser === null)
        {
            return false;
        }
        $user=$this->userRepository->findOneBy(['email' => $this->infoUser['email']]);

        if (!$user instanceof (User::class) || $this->infoUser['password'] !== $user->getPassword())
        {
            return false;
        }
        $this->session->set('user',$user);
        return true;
    }

    public function isAuthorized():string
    {
        if ($this->session->get('user')!=null)
        {
            if ($this->session->get('user')->getRole()!== 'user')
            {
                return 'authorized';
            }
            else
            {
                return 'unauthorized';
            }
        }
        else
        {
            return 'notLoggedIn';
        }
    }
}