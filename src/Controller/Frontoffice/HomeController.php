<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Service\Http\Session\Token;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Service\FormValidator\InputFormValidator;

use App\Service\Http\SendEmail;

class HomeController
{
    public function __construct(private View $view, private Session $session)
    {
    }
    public function displayHomeAction(Request $request): Response
    {
        $response = new Response($this->view->render(
            [
                "template" => 'home',
                "office" => 'frontoffice',
            ]));

        $token = new Token($this->session);
        $token->setToken();

        if ($request->getMethod() === 'POST')
        {
            if ($token->verifyToken($request))
            {
                $contactFormValidator= new InputFormValidator($request);

                $firstName = $request->request()->get('firstname');
                $name = $request->request()->get('name');
                $email = $request->request()->get('email');
                $message = $request->request()->get('message');
                $rgpd = $request->request()->get('rgpd');

                $isFirstnameValid = $contactFormValidator->isInputValid("/^[A-Za-z- _]+$/", $firstName);
                $isNameValid = $contactFormValidator->isInputValid("/^[A-Za-z- _]+$/", $name);
                $isEmailValid = $contactFormValidator->isEmailValid($email);
                $isMessageValid = $contactFormValidator->isTextareaValid($message);
                $isRgpdChecked = $contactFormValidator->isRgpdChecked($rgpd);

                if ($isFirstnameValid
                    && $isNameValid
                    && $isEmailValid
                    && $isMessageValid
                    && $isRgpdChecked
                )
                {
                    $this->session->addFlashes('success','Formulaire valide, votre message : "'.$message.'" est bien envoyé!');
                }

                if (!$isFirstnameValid)
                {
                    $this->session->addFlashes('error',"Votre prénom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
                }
                if (!$isNameValid)
                {
                    $this->session->addFlashes('error',"Votre nom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
                }
                if (!$isEmailValid)
                {
                    $this->session->addFlashes('error',"Votre email ne correspond pas.");
                }
                if (!$isMessageValid)
                {
                    $this->session->addFlashes('error',"Votre message ne peut pas être vide, écrivez-nous quelque chose!");
                }
                if (!$isRgpdChecked)
                {
                    $this->session->addFlashes('error',"Vous avez oublié la checkbox!");
                }

                $mail = new SendEmail();
                if ($mail->sendMail('david.renard@g2a-consulting.fr', $name . ' ' . $firstName, 'Message de contact de ' . $email, $message))
                {
    //                var_dump($mail);die;
                    return $response;
                }
            }
            else
            {
                $this->session->addFlashes('error','Il semblerait que ce ne soit pas vous qui tentez de nous contacter!?');
                $response->redirect();
            }
        }
        return $response;
    }
}