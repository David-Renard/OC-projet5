<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Service\FormValidator\ContactFormValidator;

class HomeController
{
    public function __construct(private View $view, private Session $session)
    {
    }
    public function displayHomeAction(Request $request = null): Response
    {
        if ($request->getMethod()==='POST')
        {
            $contactFormValidator= new ContactFormValidator($request, $this->session);
            if (($contactFormValidator->isInputValid("/[0-9.@#]+/",$request->query()->get('firstName')))
            && ($contactFormValidator->isInputValid("/[0-9.@#]+/",$request->query()->get('name')))
            && ($contactFormValidator->isEmailValid($request->query()->get('email'))))
            {
                $this->session->addFlashes('success','formulaire valide');
            }
            $this->session->addFlashes('error','il y a une erreur dans au moins une saisie');

            return new Response($this->view->render([
                'template' => 'home',
                'data'=>[]
            ]));
        }
        return new Response($this->view->render([
            'template' => 'home',
        ]));
    }
}