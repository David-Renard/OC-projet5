<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

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
    public function displayPostsAction(string $status): Response
    {
        $posts = $this->postRepository->findBy(['status'=>$status],['creationDate'=>'DESC']);
        return new Response($this->view->render([
            'template' => 'posts',
            'data' => ['posts' => $posts],
            "office" => 'frontoffice',
        ]));
    }

    // private method used in order to display both post and post's comments in an array
    private function postCommentsArray(int $id, CommentRepository $commentRepository): ?Array
    {
        $array=[];
        $post = $this->postRepository->findOneBy(
            [
                'ID'=>$id,
            ]
        );

        if ($post !== null)
        {
            $comments = $commentRepository -> findBy(['id_post'=>$id,'status'=>'valided'],['creationDate'=>'ASC']);

            $array = [
                'post' => $post,
                'comments' => $comments,
                "office" => 'frontoffice',
            ];
        }
        return $array;
    }

    public function displayPostAction(int $id, CommentRepository $commentRepository): Response
    {
        $response = new Response("Ce post n'existe pas ou plus, <a href='index.php?action=posts'>revenir à la page des posts.</a>");
        $postCommentsArray = $this->postCommentsArray($id,$commentRepository);

        if ($postCommentsArray == [])
        {
            return $response;
        }
        else
        {
            $response = new Response($this->view->render(
                [
                    'template' => 'post',
                    'data' => $postCommentsArray,
                    "office" => 'frontoffice',
                ],
            ));
            return $response;
        }
    }

    public function addComment(Request $request, CommentRepository $commentRepository): Response
    {
        $response = new Response('nécessite connexion',200);
        if ($request->getMethod() === 'POST')
        {
            $commentFormValidator = new CommentFormValidator($request, $this->session);
            $user = $this->session->get('user');
            $isContentValid = $commentFormValidator->isTextareaValid($request->request()->get('content'));
            $isLogged = $commentFormValidator->isLogged();

            if ($isContentValid && $isLogged)
            {
                $idAuthor = $user->getId();
                $idPost = $request->query()->get('id');
                $content = $request->request()->get('content');

                $newComment=new Comment();
                $newComment->setContent(htmlspecialchars($content));
                $newComment->setIdPost((int)$idPost);
                $newComment->setIdAuthor($idAuthor);

                $postCommentsArray = $this->postCommentsArray(intval($idPost),$commentRepository);

                if ($commentRepository->create($newComment))
                {
                    $this->session->addFlashes('success','Votre commentaire "' . $content . '" est ajouté et en attente de validation.');
                }
                else
                {
                    $this->session->addFlashes('error','Votre commentaire "' . $content . '" n\'a pas pu être ajouté.');
                }

                $response = new Response($this->view->render([
                    'template' => 'post',
                    'data' => $postCommentsArray,
                    "office" => 'frontoffice',
            ]));
                $response -> redirect('?action=post&id=' . $idPost);
//        ?><!--<pre>--><?php
//        var_dump($this->session);die;
//        ?><!--</pre>--><?php
            }

            if (!$isContentValid)
            {
                $this->session->addFlashes('error','Faites-nous part de votre commentaire, ne nous envoyez pas un message vide!');
            }
            if (!$isLogged)
            {
                $this->session->addFlashes('error','Vous devez être connecté pour pouvoir commenter ce post!');
                $response->redirect('?action=login');
            }
        }
        return $response;
    }
}