<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;

class CommentController
{
    public function __construct(private CommentRepository $commentRepository, private View $view)
    {
    }
    public function displayCommentsAction(): Response
    {
        $posts = $this->commentRepository->findAll();
        return new Response($this->view->render([
            'template' => 'posts',
            'data' => ['posts' => $posts],
        ]));
    }

}