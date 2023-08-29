<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Service\FormValidator\LoginFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Model\Repository\UserRepository;
use App\Service\Http\Session\Session;

class UserController
{
    public function __construct(private UserRepository $userRepository, private View $view, private Session $session)
    {
    }

    public function displayHomeAdmin(Request $request): Response
    {
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();
        if ($authorizationLevel === 'unauthorized')
        {
            $this->session->addFlashes("error","Vous ne disposez pas des droits d'accès à cette partie du site.");
        }
        elseif ($authorizationLevel === 'authorized')
        {
            $this->session->addFlashes('success','Bienvenue sur la partie administration du site');
        }
        else
        {
            $this->session->addFlashes("error","Vous devez être connecté et disposer des droits d'accès pour accéder à cette partie du site.");
        }

        return new Response($this->view->render(
            [
                "template" => 'admin',
                'office' => 'backoffice',
            ]));
    }
}