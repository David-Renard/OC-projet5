<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Model\Entity\Post;
use App\Service\FormValidator\LoginFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Model\Repository\UserRepository;
use App\Model\Entity\User;
use App\Service\Http\Session\Session;

class UserController
{
    public function __construct(private UserRepository $userRepository, private View $view, private Session $session)
    {
    }

    public function displayHomeAdmin(Request $request): Response
    {
        $response = new Response();
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();
        if ($authorizationLevel > 0)
        {
            $response -> redirect('?action=adminposts');
        }
        else
        {
            $response -> redirect();
        }
        $response = new Response($this->view->render(
            [
                "template" => 'admin',
                'office' => 'backoffice',
            ]));
        return $response;
    }

    public function displayUsers(Request $request): Response
    {
        $adminsUsers = $this->userRepository->findBy(['role' => 'admin']);
        $usersUsers = $this->userRepository->findBy(['role' => 'user']);
        $users = array_merge($adminsUsers, $usersUsers);

        $response = new Response($this->view->render(
        [
            "template" => 'adminusers',
            'data' => [
                'users' => $users,
            ],
            'office' => 'backoffice',
        ]));
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        if ($authorizationLevel < 2)
        {
            $response->redirect();
        }
        return $response;
    }

    public function updateUser(Request $request): Response
    {
        $response = new Response($this->view->render([
            'template' => 'adminusers',
            'office' => 'backoffice',
        ]));
        $userId = intval($request->query()->get('id'));
        $currentRole = $this->userRepository->find($userId)->getRole();

        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        if ($authorizationLevel < 2)
        {
            $response->redirect();
        }

        $user = new User();
        if ($currentRole === 'admin')
        {
            $user->setRole('user');
        }
        else
        {
            $user->setRole('admin');
        }
        $user->setId($userId);

        if ($this->userRepository->update($user))
        {
            $this->session->addFlashes('success','Le rôle a bien été modifié avec succès.');
        }
        else
        {
            $this->session->addFlashes('error','Le rôle n\'a pas pu être modifié.');
        }
        $response->redirect('?action=adminusers');

        return $response;
    }

    public function deleteUser(Request $request, PostRepository $postRepository): Response
    {
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        $response = new Response('',301,[]);

        if ($authorizationLevel < 2)
        {
            $response->redirect();
        }

        if ($request->query()->has('id'))
        {
            $idUser = intval($request->query()->get('id'));
            $user = new User();
            $user->setId($idUser);
            $userPost = $postRepository->findBy([':id' => $idUser]);
            var_dump($userPost);die;

            if ($userPost != [])
            {
                foreach($userPost as $currentPost)
                {
                    $post = new Post();
                    $post->setId($currentPost->getId());
                    $post->setTitle($currentPost->getTitle());
                    $post->setLede($currentPost->getLede());
                    $post->setContent($currentPost->getContent());
                    $post->setLastUpdateDate($currentPost->getUpdateDate());
                    $post->setCreationDate($currentPost->getCreationDate());
                    $post->setIdAuthor(32);
//                    var_dump($post);die;
                    $postRepository->update($post);
                }
            }

            if ($this->userRepository->delete($user))
            {
                $this->session->addFlashes('success', 'L\'utilisateur a bien été supprimé.');
            }
            else
            {
                $this->session->addFlashes('error',"L'utilisateur n'a pas pu être supprimé.");
            }
        }
        $response->redirect('?action=adminusers');
        return $response;
    }
}