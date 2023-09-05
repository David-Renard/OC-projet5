<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Entity\Comment;
use App\Model\Entity\Post;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Model\Repository\PostRepository;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\UserRepository;

class PostController
{
    public function __construct(private PostRepository $postRepository, private View $view, private Session $session)
    {
    }

    private function displayCommentsByState(string $status, CommentRepository $commentRepository)
    {
        $posts = $this -> postRepository -> findAll();
        foreach ($posts as $post)
        {
            $post -> setComments($commentRepository -> findBy(['id_post'=>$post->getID(),'status'=>$status]));
        }

        return $posts;
    }

    public function getCommentsByState(string $status, CommentRepository $commentRepository, Request $request): Response
    {
        $posts = $this->displayCommentsByState($status,$commentRepository);
//        $count=$this->postRepository->count($posts);
//            ?><!--<pre>--><?php
//            var_dump($count['Total']);die;
//            ?><!--<pre>--><?php

        $response = new Response($this->view->render([
            'template' => 'admincomments',
            'data' => [
                'posts' => $posts,
            ],
            "office" => 'backoffice',
        ]));

        if ($request -> getMethod() === 'POST')
        {
            $commentStatus = $request->request()->get('states');

//            ?><!--<pre>--><?php
//            var_dump($commentStatus,$count,$status);die;
//            ?><!--<pre>--><?php

            if ($commentStatus === null)
            {
                $this->session->addFlashes('success', "Il n'y a pas de commentaires à mettre à jour.");
//                $response->redirect('?action=admincomments');
            }
            else
            {
                foreach ($commentStatus as $key => $states)
                {
                    $comment = new Comment();
                    $comment->setStatus($states);
                    $comment->setId($key);
//                    $count=$commentRepository->count($comment);
//            ?><!--<pre>--><?php
//            var_dump($count['Total']);die;
//            ?><!--<pre>--><?php
                    $commentRepository->update($comment);
                }
                $this->session->addFlashes('success', 'Les commentaires ont été mis à jour.');
                $response->redirect('?action=admincomments');
            }
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
//            ?><!--<pre>--><?php
//            var_dump($posts);die;
//            ?><!--<pre>--><?php
        return $response;
    }

    public function addPost(Request $request, PostRepository $postRepository): Response
    {
        $response = new Response($this->view->render([
            'template' => 'adminpostadd',
            'office' => 'backoffice',
        ]));
        //            ?><!--<pre>--><?php
        //            var_dump($posts);die;
        //            ?><!--<pre>--><?php

        if ($request->getMethod() === 'POST')
        {
            $post = new Post();
            $post->setTitle($request->request()->get('title'));
            $post->setLede($request->request()->get('lede'));
            $post->setContent($request->request()->get('content'));
            $post->setStatus($request->request()->get('status'));
            $post->setIdAuthor(intval($this->session->get('user')->getId()));

            if ($postRepository->create($post))
            {
                $this->session->addFlashes('success','Le post a bien été ajouté.');
                $response->redirect('?action=adminposts');
            }
            else
            {
                $this->session->addFlashes('error','Le post n\'a pas pu être ajouté.');
            }
        }
        return $response;
    }

    public function updatePost(Request $request, PostRepository $postRepository): Response
    {
        $response = new Response();

        if ($request->getMethod() === 'GET')
        {
            $arrayStatus = ['published','deleted'];
            $idPost = $request->query()->get('id');
            $post = $this->postRepository->find((int) $idPost);

            $response = new Response($this->view->render([
            'template' => 'updatepost',
            'office' => 'backoffice',
            'data' => [
                'post' => $post,
                'arrayStatus' => $arrayStatus,
            ]
        ]));
        }

        if ($request->getMethod() === 'POST')
        {
            $post = new Post();
            $post->setTitle($request->request()->get('title'));
            $post->setLede($request->request()->get('lede'));
            $post->setContent($request->request()->get('content'));
            $post->setStatus($request->request()->get('status'));
            $post->setId(intval($request->query()->get('id')));

            if ($postRepository->update($post))
            {
                $this->session->addFlashes('success','Le post a bien été mis à jour.');
                $response->redirect('?action=adminposts');
            }
            else
            {
                $this->session->addFlashes('error','Le post n\'a pas pu être mis à jour.');
            }
        }

        return $response;
    }
}