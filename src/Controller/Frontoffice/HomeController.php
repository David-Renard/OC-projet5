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
    public function displayHomeAction(Request $request): Response
    {
        if ($request->getMethod()==='POST')
        {
            $contactFormValidator= new ContactFormValidator($request, $this->session);

            $isFirstnameValid=$contactFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('firstname'));
            $isNameValid=$contactFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('name'));
            $isEmailValid=$contactFormValidator->isEmailValid($request->request()->get('email'));
            $isMessageValid=$contactFormValidator->isTextareaValid($request->request()->get('message'));
            $isRgpdChecked=$contactFormValidator->isRgpdChecked($request->request()->get('rgpd'));

            if ($isFirstnameValid
                && $isNameValid
                && $isEmailValid
                && $isMessageValid
                && $isRgpdChecked)
            {
                $this->session->addFlashes('success','Formulaire valide, votre message : "'.$request->request()->get("message").'" est bien envoyé!');
            }

            if (!$isFirstnameValid)
            {
                $this->session->addFlashes('error',"Votre prénom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ','-' et '_')");
            }
            if (!$isNameValid)
            {
                $this->session->addFlashes('error',"Votre nom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ','-' et '_')");
            }
            if (!$isEmailValid)
            {
                $this->session->addFlashes('error',"Votre email ne correspond pas");
            }
            if (!$isMessageValid)
            {
                $this->session->addFlashes('error',"Votre message ne peut pas être vide, écrivez-nous quelque chose!");
            }
            if (!$isRgpdChecked)
            {
                $this->session->addFlashes('error',"Vous avez oublié la checkbox!");
            }
        }
        return new Response($this->view->render(
            ["template" => 'home',
            ]));
    }
}