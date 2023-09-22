<?php
declare(strict_types=1);

namespace App\Service\FormValidator;

use App\Model\Entity\User;
use App\Service\Http\Request;
use App\Model\Repository\UserRepository;
use App\Service\Http\Session\Session;

class LoginFormValidator
{
    private ?array $infoUser = [];

    public function __construct(private Request $request, private UserRepository $userRepository, private Session $session)
    {
        $this->infoUser = $this->request->request()->all();
    }

    public function isValid(): bool
    {
        if ($this->infoUser === null) {
            return false;
        }
        $user = $this->userRepository->findOneBy([
            'email' => $this->infoUser['email']
        ]);

        if ($user instanceof (User::class) === false
            || password_verify($this->infoUser['password'], $user->getPassword()) === false
        ) {
            return false;
        }
        $this->session->set('user', $user);
        return true;
    }

    /** the int returned is a level authorization, 0 for user, 1 for admin and 2 for super-admin
     * @return int
     */
    public function isAuthorized(): int
    {
        $return = 0;
        if ($this->session->get('user') != null) {
            if ($this->session->get('user')->getRole() === 'super_admin') {
                $return = 2;
            } elseif ($this->session->get('user')->getRole() !== 'user') {
                $this->session->addFlashes('unauthorized', 'Vous ne disposez pas des droits d\'accès à cette partie du site.');
                $return = 1;
            } else {
                $this->session->addFlashes("unauthorized", "Vous ne disposez pas des droits d'accès à la partie administration du site.");
            }
        } else {
            $this->session->addFlashes("unauthorized", "Vous devez être connecté et disposer des droits d'accès pour accéder à la partie administration du site.");
        }
        return $return;
    }
}