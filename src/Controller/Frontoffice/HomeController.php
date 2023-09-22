<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Service\Token;
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
        $response = new Response();

        $token = new Token($this->session);

        if ($request->getMethod() === 'GET') {
            $token->setToken();
        } elseif ($request->getMethod() === 'POST') {
            if ($token->verifyToken($request)) {
                $contactFormValidator = new InputFormValidator($request);

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
                ) {
                    $this->session->addFlashes('success', 'Formulaire valide, votre message : "' . $message . '" est bien envoyé!');
                }

                if ($isFirstnameValid === false) {
                    $this->session->addFlashes('error', "Votre prénom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
                }
                if ($isNameValid === false) {
                    $this->session->addFlashes('error', "Votre nom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
                }
                if ($isEmailValid === false) {
                    $this->session->addFlashes('error', "Votre email ne correspond pas.");
                }
                if ($isMessageValid === false) {
                    $this->session->addFlashes('error', "Votre message ne peut pas être vide, écrivez-nous quelque chose!");
                }
                if ($isRgpdChecked === false) {
                    $this->session->addFlashes('error', "Vous avez oublié la checkbox!");
                }

                $mail = new SendEmail();
                $fullName = $name . ' ' . $firstName;
                $subject = "Message de contact de $fullName";
                $bodyMail = "Vous avez reçu un nouveau message de contact de $fullName : $message";

                if ($mail->sendMail($email, $fullName, $subject, $bodyMail)) {
                    $response->redirect();
                }
            } elseif ($token->verifyToken($request) === false) {
                $this->session->addFlashes('error', 'Il semblerait que ce ne soit pas vous qui tentez de nous contacter!?');
                $response->redirect();
            }
        }
        return new Response($this->view->render(
            [
                "template" => 'home',
                "office" => 'frontoffice',
            ]));
    }
}