<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Entity\Comment;
use App\Service\FormValidator\CommentFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Model\Repository\PostRepository;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\UserRepository;
use App\Service\FormValidator\LoginFormValidator;

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
                    $commentRepository->update($comment);
//            ?><!--<pre>--><?php
//            var_dump($comment);die;
//            ?><!--<pre>--><?php
                }

                $this->session->addFlashes('success', 'Les commentaires ont été mis à jour.');
            }
        }

        return $response;
    }
}