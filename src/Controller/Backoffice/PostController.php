<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Entity\Comment;
use App\Model\Entity\Post;
use App\Service\FormValidator\LoginFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Service\Token;
use App\Model\Repository\PostRepository;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\UserRepository;
use App\Service\FormValidator\InputFormValidator;

class PostController
{
    public function __construct(private PostRepository $postRepository, private View $view, private Session $session, private UserRepository $userRepository)
    {
    }

    private function displayCommentsByState(string $status, CommentRepository $commentRepository): ?array
    {
        $posts = $this->postRepository->findAll();
        foreach ($posts as $post)
        {
            $post->setComments($commentRepository->findBy(['idPost' => $post->getID(),'status' => $status]));
        }

        return $posts;
    }

    public function getCommentsByState(string $status, CommentRepository $commentRepository, Request $request): Response
    {
        $posts = $this->displayCommentsByState($status,$commentRepository);
        $response = new Response($this->view->render([
            'template' => 'admincomments',
            'data' => [
                'posts' => $posts,
            ],
            "office" => 'backoffice',
        ]));
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        if ($authorizationLevel < 1)
        {
            $response->redirect();
        }
        return $response;
    }

    public function displayPost(Request $request): Response
    {
        $posts = $this->postRepository->findAll();
        $response = new Response($this->view->render([
            'template' => 'adminposts',
            'data' => [
                'posts' => $posts,
            ],
            'office' => 'backoffice',
            ]));

        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        if ($authorizationLevel < 1)
        {
            $response->redirect();
        }
        return $response;
    }

    public function addPost(Request $request, PostRepository $postRepository, array $inputs=[]): Response
    {
        $response = new Response();
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        if ($authorizationLevel < 1)
        {
            $response->redirect();
        }

        $token = new Token($this->session);

        if ($request->getMethod() === 'GET' && $request->query()->get('action') === 'adminpostadd')
        {
            $token->setToken();
        }
        elseif ($request->getMethod() === 'POST')
        {
            if ($token->verifyToken($request))
            {
                $title = $request->request()->get('title');
                $lede = $request->request()->get('lede');
                $content = $request->request()->get('content');
                $isTitleTaken = $this->postRepository->findOneBy(['title' => $title]);

                $addPostValidator = new InputFormValidator($request);
                $isTitleEmpty = $addPostValidator->isEmpty($title);
                $isLedeEmpty = $addPostValidator->isEmpty($lede);
                $isContentEmpty = $addPostValidator->isEmpty($content);
                $isTitleOk = $addPostValidator->isNotToLong($title,120);
                $isLedeOk = $addPostValidator->isNotToLong($lede,255);
                $isContentOk = $addPostValidator->isNotToLong($content,65535);

                if (!$isTitleEmpty
                    && !$isLedeEmpty
                    && !$isContentEmpty
                    && $isTitleOk
                    && $isLedeOk
                    && $isContentOk
                    && !$isTitleTaken)
                {
                    $post = new Post();
                    $post->setTitle($title);
                    $post->setLede($lede);
                    $post->setContent($content);
                    $post->setIdAuthor(intval($this->session->get('user')->getId()));

                    if ($postRepository->create($post)) {
                        $this->session->addFlashes('success', 'Le post a bien été ajouté.');
                        $response->redirect('?action=adminposts');
                    }
                    else {
                        $this->session->addFlashes('error', 'Le post n\'a pas pu être ajouté.');
                    }
                }
                elseif (empty($inputs))
                {
                    if ($isTitleEmpty)
                    {
                        $this->session->addFlashes('error','Le titre de l\'article ne peut pas être vide.');
                    }
                    if ($isLedeEmpty)
                    {
                        $this->session->addFlashes('error','Le chapô de l\'article ne peut pas être vide.');
                    }
                    if ($isContentEmpty)
                    {
                        $this->session->addFlashes('error','Le contenu de l\'article ne peut pas être vide.');
                    }
                    if (!$isTitleOk)
                    {
                        $this->session->addFlashes('error','Le titre de l\'article est trop long.');
                    }
                    if (!$isLedeOk)
                    {
                        $this->session->addFlashes('error','Le chapô de l\'article est trop long.');
                    }
                    if (!$isContentOk)
                    {
                        $this->session->addFlashes('error','Le contenu de l\'article est trop long.');
                    }
                    if ($isTitleTaken)
                    {
                        $this->session->addFlashes('error','Ce titre est déjà attribué à un autre post.');
                    }

                    $inputs = [
                        'title' => $title,
                        'lede' => $lede,
                        'content' => $content,
                    ];
                    //return this function this time with inputs
                    return $this->addPost($request,$postRepository,$inputs);
                }
            }
            elseif (!$token->verifyToken($request))
            {
                $this->session->addFlashes('error','Il semblerait que ce ne soit pas vous qui tentez d\'ajouter un post!?');
                $response->redirect('?action=adminpostadd');
            }
        }

        return new Response($this->view->render([
            'template' => 'adminpostadd',
            'office' => 'backoffice',
            'data' => [
                'inputs' => $inputs,
            ]
        ]));
    }

    public function updatePost(Request $request, PostRepository $postRepository, array $inputs=[]): Response
    {
        $response = new Response();
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        if ($authorizationLevel < 1)
        {
            $response->redirect();
        }

        $token = new Token($this->session);

        $updateAuthor = $this->session->get('user');
        $idPost = $request->query()->get('id');
        $post = $this->postRepository->find((int) $idPost);
        $originalAuthor = $post->getIdAuthor();

        if ($request->getMethod() === 'GET' && $request->query()->get('action') === 'adminupdatepost')
        {
            $token->setToken();
        }
        elseif ($request->getMethod() === 'POST')
        {
            if ($token->verifyToken($request))
            {
                $title = $request->request()->get('title');
                $lede = $request->request()->get('lede');
                $content = $request->request()->get('content');
                $isModifiedBy = $request->request()->get('updateAuthor');

                $isTitleTaken = $this->postRepository->findOneBy(['title' => $title]);
                if (!empty($isTitleTaken) && $isTitleTaken->getId() === intval($idPost))
                {
                    $isTitleTaken = false;
                }

                $updatePostValidator = new InputFormValidator($request);
                $isTitleEmpty = $updatePostValidator->isEmpty($title);
                $isLedeEmpty = $updatePostValidator->isEmpty($lede);
                $isContentEmpty = $updatePostValidator->isEmpty($content);
                $isTitleOk = $updatePostValidator->isNotToLong($title,120);
                $isLedeOk = $updatePostValidator->isNotToLong($lede,255);
                $isContentOk = $updatePostValidator->isNotToLong($content,65535);

                if (!$isTitleEmpty
                    && !$isLedeEmpty
                    && !$isContentEmpty
                    && $isTitleOk
                    && $isLedeOk
                    && $isContentOk
                    && !$isTitleTaken)
                {
                    $post = new Post();
                    $post->setTitle($title);
                    $post->setLede($lede);
                    $post->setContent($content);
                    $post->setId(intval($request->query()->get('id')));
                    if ($isModifiedBy != null)
                    {
                        $post->setIdAuthor(intval($updateAuthor->getId()));
                    }
                    else
                    {
                        $post->setIdAuthor($originalAuthor);
                    }

                    if ($postRepository->update($post))
                    {
                        $this->session->addFlashes('success','Le post n°'. $idPost .' a bien été mis à jour.');
                        $response->redirect('?action=adminposts');
                    }
                    else
                    {
                        $this->session->addFlashes('error','Le post n\'a pas pu être mis à jour.');
                    }
                }
                elseif (empty($inputs))
                {
                    if ($isTitleEmpty)
                    {
                        $this->session->addFlashes('error','Le titre de l\'article ne peut pas être vide.');
                    }
                    if ($isLedeEmpty)
                    {
                        $this->session->addFlashes('error','Le chapô de l\'article ne peut pas être vide.');
                    }
                    if ($isContentEmpty)
                    {
                        $this->session->addFlashes('error','Le contenu de l\'article ne peut pas être vide.');
                    }
                    if (!$isTitleOk)
                    {
                        $this->session->addFlashes('error','Le titre de l\'article est trop long.');
                    }
                    if (!$isLedeOk)
                    {
                        $this->session->addFlashes('error','Le chapô de l\'article est trop long.');
                    }
                    if (!$isContentOk)
                    {
                        $this->session->addFlashes('error','Le contenu de l\'article est trop long.');
                    }
                    if ($isTitleTaken)
                    {
                        $this->session->addFlashes('error','Ce titre est déjà attribué à un autre post.');
                    }

                    $inputs = [
                        'title' => $title,
                        'lede' => $lede,
                        'content' => $content,
                    ];
                    return $this->updatePost($request,$postRepository,$inputs);
                }
            }
            elseif (!$token->verifyToken($request))
            {
                $this->session->addFlashes('error','Il semblerait que ce ne soit pas vous qui tentez de modifier un post!?');
                $response->redirect('?action=adminupdatepost&id=' . $idPost);
            }
        }
        return new Response($this->view->render([
            'template' => 'adminupdatepost',
            'office' => 'backoffice',
            'data' => [
                'post' => $post,
                'updateauthor' => $updateAuthor,
                'inputs' => $inputs,
            ]
        ]));
    }

    public function deletePost(Request $request, PostRepository $postRepository): Response
    {
        $response = new Response($this->view->render([
            'template' => 'adminposts',
            'office' => 'backoffice',
        ]));

        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();

        if ($request->getMethod() === 'GET' && $authorizationLevel > 0)
        {
            $post = new Post();
            $post->setId(intval($request->query()->get('id')));

            if ($postRepository->delete($post))
            {
                $this->session->addFlashes('success','Le post a bien été supprimé.');
            }
            else
            {
                $this->session->addFlashes('error','Le post n\'a pas pu être supprimé.');
            }
            $response->redirect('?action=adminposts');
        }
        if ($authorizationLevel === 0)
        {
            $response->redirect();
        }
        return $response;
    }

    public function moderateComment(CommentRepository $commentRepository, Request $request): Response
    {
        if ($request->query()->has('id') && $request->query()->has('moderate'))
        {
            $commentId = intval($request->query()->get('id'));
            $commentStatus = $request->query()->get('moderate');

            $comment = new Comment();
            $comment->setId($commentId);
            $comment->setStatus($commentStatus);

            if ($commentRepository->update($comment))
            {
                $this->session->addFlashes('success','Le commentaire a bien été modéré.');
            }
            else
            {
                $this->session->addFlashes('error','Le commentaire n\'a pas pu être modéré.');
            }
        }
        $response = new Response();
        $response->redirect('?action=admincomments');
        return $response;
    }
}