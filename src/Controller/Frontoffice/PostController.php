<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Model\Repository\PostRepository;
use App\Service\FormValidator\LoginFormValidator;

class PostController
{
    public function __construct(private PostRepository $postRepository, private View $view)
    {
    }

    public function loginAction(Request $request): Response
    {
//        if ($request->getMethod() === 'POST') {
//            $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
//            if ($loginFormValidator->isValid()) {
//                return new Response("Utilisateur connecté : <a href='index.php'>Revenir à la page d'accueil</a>", 200);
//            }
//            $this->session->addFlashes('error', 'mauvais identifiants');
//        }
//        return new Response($this->view->render(['template' => 'login', 'data' => []]));
    }

    public function displayPostsAction(): Response
    {
        $posts = $this->postRepository->findAll();
        return new Response($this->view->render([
            'template' => 'posts',
            'data' => ['posts' => $posts],
        ]));
    }

}