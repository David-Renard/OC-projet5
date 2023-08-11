<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\Frontoffice\UserController;
use App\Controller\Frontoffice\PostController;
use App\Controller\Frontoffice\HomeController;
use App\Controller\Frontoffice\CommentController;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\UserRepository;
use App\Model\Repository\PostRepository;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\View\View;
use App\Service\Http\Session\Session;

class Router
{
    private DatabaseConnection $database;
    private View $view;
    private Session $session;

    public function __construct(private Request $request)
    {
        $this->database = new DatabaseConnection();
        $this->session= new Session();
        $this->view = new View($this->session);
    }
    public function run(): Response
    {
        $action = ($this->request->query()->has('action')) ? $this->request->query()->get('action') : 'home';

        if ($action === 'login')
        {
            $userRepository = new UserRepository($this->database);
            $controller = new UserController($userRepository,$this->view, $this->session);
            return $controller->loginAction($this->request);
        }
        if ($action === 'users')
        {
            $userRepository = new UserRepository($this->database);
            $controller = new UserController($userRepository,$this->view, $this->session);
            return $controller->displayAllAction();
        }
        if ($action === 'logout')
        {
            $userRepository = new UserRepository($this->database);
            $controller = new UserController($userRepository, $this->view, $this->session);
            return $controller->logoutAction();
        }
        if ($action === 'home')
        {
            $controller = new HomeController($this->view);
            return $controller->displayHomeAction();
        }
        if ($action === 'posts')
        {
            $postRepository = new PostRepository($this->database);
            $controller = new PostController($postRepository, $this->view);
            return $controller->displayPostsAction();
        }
        if ($action === 'post' && $this->request->query()->has('id'))
        {
            $postRepository = new PostRepository($this->database);
            $controller = new PostController($postRepository, $this->view);
            $commentRepository = new CommentRepository($this->database);
            return $controller->displayPostAction((int) $this->request->query()->get('id'),$commentRepository);
        }
        return new Response("Error 404 - cette page n'existe pas<br><a href='index.php'>Aller Ici</a>", 404);
    }
}