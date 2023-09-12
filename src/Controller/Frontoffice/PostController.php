<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Model\Entity\Comment;
use App\Service\FormValidator\InputFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Pagination;
use App\Service\Http\Session\Session;
use App\Model\Repository\PostRepository;
use App\Model\Repository\CommentRepository;

class PostController
{
    public function __construct(private PostRepository $postRepository, private View $view, private Session $session)
    {
    }
    public function displayPostsAction(string $status, Request $request): Response
    {
        $posts = $this->postRepository->findBy(['status' => $status],['creationDate' => 'DESC']);
        $pagination = new Pagination($request,$posts);
//        var_dump($this->postRepository->count());die;
        $limit = $pagination->nbPerPage();
        $nbPages = $pagination->nbPages();
        $current = $pagination->currentPage();
        $offset = $limit * ($current - 1);
        $paginationArray = [
                'limit' => $limit,
                'nbPages' => $nbPages,
                'current' => $current,
            ];
//        ?><!--<pre>--><?php
//        var_dump($limit);die;
//        ?><!--<pre>--><?php
        $posts = $this->postRepository->findBy(['status' => $status],['creationDate' => 'DESC'], $limit, $offset);

        return new Response($this->view->render([
            'template' => 'posts',
            'data' => [
                    'posts' => $posts,
                    'pagination' => $paginationArray,
                ],
            "office" => 'frontoffice',
        ]));
    }

    // private method used in order to display both post and post's comments in an array
    private function postCommentsArray(int $id, CommentRepository $commentRepository): ?Array
    {
        $array=[];
        $post = $this->postRepository->findOneBy(
            [
                'id'=>$id,
            ]
        );

        if ($post !== null)
        {
            $comments = $commentRepository -> findBy(['id_post'=>$id,'status'=>'valided']);

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
        $response = new Response();
        $postCommentsArray = $this->postCommentsArray($id,$commentRepository);
//        var_dump($postCommentsArray);die;
        if ($postCommentsArray == [])
        {
            $this->session->addFlashes('error','Ce post n\'existe pas ou plus. Vous avez été redirigé vers l\'ensemble des posts.');
            $response->redirect('?action=posts');
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
        }
        return $response;
    }

    public function addComment(Request $request, CommentRepository $commentRepository): Response
    {
        if ($request->getMethod() === 'POST')
        {
            $commentFormValidator = new InputFormValidator($request, $this->session);
            $user = $this->session->get('user');
            $isContentValid = $commentFormValidator->isTextareaValid($request->request()->get('content'));

            $redirectResponse = new Response();
            $idPost = $request->query()->get('id');
            if ($isContentValid)
            {
                $idAuthor = $user->getId();
                $content = $request->request()->get('content');

                $newComment = new Comment();
                $newComment->setContent(htmlspecialchars($content));
                $newComment->setIdPost((int)$idPost);
                $newComment->setIdAuthor($idAuthor);

                if ($commentRepository->create($newComment))
                {
                    $this->session->addFlashes('success','Votre commentaire "' . html_entity_decode($content) . '" est ajouté et en attente de validation.');
                }
                else
                {
                    $this->session->addFlashes('error','Votre commentaire "' . html_entity_decode($content) . '" n\'a pas pu être ajouté.');
                }
            }
            else
            {
                $this->session->addFlashes('error','Faites-nous part de votre commentaire, ne nous envoyez pas un message vide!');
            }
        }
        else
        {
            $idPost=1;
        }
//        ?><!--<pre>--><?php
//        var_dump($this->session->getFlashes());die;
//        ?><!--</pre>--><?php
        $redirectResponse->redirect('?action=post&id=' . $idPost);
        return $redirectResponse;
    }
}