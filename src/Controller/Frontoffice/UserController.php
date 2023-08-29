<?php
declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Model\Repository\UserRepository;
use App\Service\FormValidator\LoginFormValidator;
class UserController
{
    public function __construct(private UserRepository $userRepository, private View $view, private Session $session)
    {
    }

    public function loginAction(Request $request): Response
    {
        if ($request->getMethod()==='POST')
        {
            $loginFormValidator=new LoginFormValidator($request, $this->userRepository, $this->session);
            if ($loginFormValidator->isValid()) {
                return new Response("Utilisateur connecté : <a href='index.php'>Revenir à la page d'accueil</a>",200);
            }
            $this->session->addFlashes('error','mauvais identifiants');
        }
        return new Response($this->view->render([
            'template' => 'login',
            "office" => 'frontoffice',
            ]));
    }

    public function logoutAction():Response
    {
        $this->session->remove('user');
        $this->session->addFlashes('logout','Vous avez bien été déconnecté.');
//        $response = new Response("Vous avez bien été déconnecté.<br/><a href='index.php'>Retourner à la page d'accueil</a>",200);
        $response = new Response($this->view->render([
            'template' => 'home',
            "office" => 'frontoffice',
        ]));
        $response->redirect();
        return $response;
    }
}