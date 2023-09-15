<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\Frontoffice\UserController;
use App\Controller\Frontoffice\PostController;
use App\Controller\Backoffice\PostController as AdminPostController;
use App\Controller\Frontoffice\HomeController;
use App\Controller\Backoffice\UserController as AdminUserController;
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
        if ($action === 'logout')
        {
            $userRepository = new UserRepository($this->database);
            $controller = new UserController($userRepository, $this->view, $this->session);
            return $controller->logoutAction();
        }
        if ($action === 'registration')
        {
            $userRepository = new UserRepository($this->database);
            $controller = new UserController($userRepository, $this->view, $this->session);
            return $controller->registration($this->request);
        }
        if ($action === 'home')
        {
            $controller = new HomeController($this->view, $this->session);
            return $controller->displayHomeAction($this->request);
        }
        if ($action === 'admin')
        {
            $userRepository = new UserRepository($this->database);
            $controller = new AdminUserController($userRepository, $this->view, $this->session);
            return $controller->displayHomeAdmin($this->request);
        }
        if ($action === 'adminusers')
        {
            $userRepository = new UserRepository($this->database);
            $controller = new AdminUserController($userRepository, $this->view, $this->session);
            return $controller->displayUsers($this->request);
        }
        if ($action === 'adminupdateuser' && $this->request->query()->has('id'))
        {
            $userRepository = new UserRepository($this->database);
            $controller = new AdminUserController($userRepository, $this->view, $this->session);
            return $controller->updateUser($this->request);
        }
        if ($action === 'admindeleteuser' && $this->request->query()->has('id'))
        {
            $userRepository = new UserRepository($this->database);
            $postRepository = new PostRepository($this->database);
            $controller = new AdminUserController($userRepository, $this->view, $this->session);
            return $controller->deleteUser($this->request, $postRepository);
        }
        if ($action === 'admincomments')
        {
            $commentRepository = new CommentRepository($this->database);
            $postRepository = new PostRepository($this->database);
            $userRepository = new UserRepository($this->database);
            $controller = new AdminPostController($postRepository, $this->view, $this->session, $userRepository);
            return $controller->getCommentsByState('awaiting',$commentRepository,$this->request);
        }
        if ($action === 'admincomment' && $this->request->query()->has('id') && $this->request->query()->has('moderate'))
        {
            $commentRepository = new CommentRepository($this->database);
            $postRepository = new PostRepository($this->database);
            $userRepository = new UserRepository($this->database);
            $controller = new AdminPostController($postRepository, $this->view, $this->session, $userRepository);
            return $controller->moderateComment($commentRepository,$this->request);
        }
        if ($action === 'adminposts')
        {
            $postRepository = new PostRepository($this->database);
            $userRepository = new UserRepository($this->database);
            $controller = new AdminPostController($postRepository, $this->view, $this->session, $userRepository);
            return $controller->displayPost($this->request);
        }
        if ($action === 'adminupdatepost' && $this->request->query()->has('id'))
        {
            $postRepository = new PostRepository($this->database);
            $userRepository = new UserRepository($this->database);
            $controller = new AdminPostController($postRepository, $this->view, $this->session, $userRepository);
            return $controller->updatePost($this->request, $postRepository);
        }
        if ($action === 'admindeletepost' && $this->request->query()->has('id'))
        {
            $postRepository = new PostRepository($this->database);
            $userRepository = new UserRepository($this->database);
            $controller = new AdminPostController($postRepository, $this->view, $this->session, $userRepository);
            return $controller->deletePost($this->request, $postRepository);
        }
        if ($action === 'adminpostadd')
        {
            $postRepository = new PostRepository($this->database);
            $userRepository = new UserRepository($this->database);
            $controller = new AdminPostController($postRepository, $this->view, $this->session, $userRepository);
            return $controller->addPost($this->request, $postRepository);
        }
        if ($action === 'posts')
        {
            $postRepository = new PostRepository($this->database);
            $controller = new PostController($postRepository, $this->view, $this->session);
            return $controller->displayPostsAction($this->request);
        }
        if ($action === 'post' && $this->request->query()->has('id') && !$this->request->request()->has('content'))
        {
            $postRepository = new PostRepository($this->database);
            $controller = new PostController($postRepository, $this->view, $this->session);
            $commentRepository = new CommentRepository($this->database);
            return $controller->displayPostAction((int) $this->request->query()->get('id'),$commentRepository);
        }
        if ($action === 'post' && $this->request->query()->has('id') && $this->request->request()->has('content'))
        {
            $postRepository = new PostRepository($this->database);
            $controller = new PostController($postRepository, $this->view, $this->session);
            $commentRepository = new CommentRepository($this->database);
            return $controller->addComment($this->request, $commentRepository);
        }
        return new Response("Error 404 - cette page n'existe pas<br><a href='index.php'>Aller Ici</a>", 404);
    }
}