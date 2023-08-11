<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Model\Entity\Comment;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Model\Repository\PostRepository;
use App\Model\Repository\CommentRepository;
use App\Service\FormValidator\LoginFormValidator;

class PostController
{
    public function __construct(private PostRepository $postRepository, private View $view)
    {
    }
    public function displayPostsAction(string $status): Response
    {
        $posts = $this->postRepository->findBy(['status'=>$status]);
        return new Response($this->view->render([
            'template' => 'posts',
            'data' => ['posts' => $posts],
        ]));
    }
    public function displayPostAction(int $id, CommentRepository $commentRepository): Response
    {
        $response = new Response("Ce post n'existe pas ou plus, <a href='index.php?action=posts'>revenir à la page des posts.</a>");
        $post = $this->postRepository->findOneBy(
            [
                'ID'=>$id,
            ]
        );

        if ($post !== null)
        {
            $comments = $commentRepository -> findBy(['id_post'=>$id]);
            $response= new Response($this->view->render(
               [
                   'template'=>'post',
                   'data'=> [
                       'post' => $post,
                       'comments' => $comments,
                   ],
               ],
            ));
        }

        return $response;
    }
}