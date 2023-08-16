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

            if ($contactFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('firstname'))
            && $contactFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('name'))
            && $contactFormValidator->isEmailValid($request->request()->get('email'))
            && $contactFormValidator->isTextareaValid($request->request()->get('message'))
            && $contactFormValidator->isRgpdChecked($request->request()->get('rgpd')))
            {
                $this->session->addFlashes('success','Formulaire valide, votre message : "'.$request->request()->get("message").'" est bien envoyé!');
            }

//            if ($contactFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('firstname'))===false)
//            {
//                $this->session->addFlashes('errorFirstname',"Votre prénom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ','-' et '_')");
//            }
//            if ($contactFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('name'))===false)
//            {
//                $this->session->addFlashes('errorName',"Votre nom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ','-' et '_')");
//            }
//            if ($contactFormValidator->isEmailValid($request->request()->get('email'))===false)
//            {
//                $this->session->addFlashes('errorEmail',"Votre email ne correspond pas");
//            }
//            if ($contactFormValidator->isTextareaValid($request->request()->get('message'))===false)
//            {
//                $this->session->addFlashes('errorMessage',"Votre message ne peut pas être vide, écrivez-nous quelque chose!");
//            }
//            if ($contactFormValidator->isRgpdChecked($request->request()->get('rgpd'))===false)
//            {
//                $this->session->addFlashes('errorRgpd',"Vous avez oublié la checkbox!");
//            }

            $this->session->addFlashes('error','Il y a au moins une erreur à la saisie du formulaire.');
        }
        return new Response($this->view->render(
            ["template" => 'home',
                ]));
    }
}