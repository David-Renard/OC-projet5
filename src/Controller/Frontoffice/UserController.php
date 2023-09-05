<?php
declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Model\Entity\User;
use App\Service\FormValidator\InputFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Model\Repository\UserRepository;
use App\Service\FormValidator\LoginFormValidator;

class UserController
{
    public function __construct(private UserRepository $userRepository, private View $view, private Session $session)
    {
    }

    public function loginAction(Request $request): Response
    {
        $response = new Response($this->view->render([
            'template' => 'login',
            "office" => 'frontoffice',
        ]));

        if ($request->getMethod()==='POST')
        {
            $loginFormValidator=new LoginFormValidator($request, $this->userRepository, $this->session);
            $isLoginFormValid=$loginFormValidator->isValid();
            if ($isLoginFormValid) {
                $this->session->addFlashes('info','Bonjour et bienvenue ' . $this->session->get('user')->getFirstname() . ' !');
                $response->redirect();
            }
            else
            {
                $this->session->addFlashes('error','Mauvais identifiant(s)');
                $response->redirect('?action=login');
            }
        }
        if ($this->session->get('user'))
        {
            $response->redirect();
        }
        return $response;
    }

    public function logoutAction():Response
    {
        $this->session->remove('user');
        $response = new Response($this->view->render([
            'template' => 'home',
            "office" => 'frontoffice',
        ]));
        $response->redirect();
        return $response;
    }

    public function registration(Request $request, UserRepository $userRepository): Response
    {
        $response = new Response($this->view->render([
            'template' => 'registration',
            'office' => 'frontoffice',
        ]));
        if ($request->getMethod() === 'POST')
        {

            $registrationFormValidator = new InputFormValidator($request,$this->session);

            $isFirstnameValid=$registrationFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('firstname'));
            $isNameValid=$registrationFormValidator->isInputValid("/^[A-Za-z- _]+$/",$request->request()->get('name'));
            $isPasswordValid=$registrationFormValidator->isInputValid("/^[A-Za-z0-9-!._]+$/",$request->request()->get('password'));
            $isEmailValid=$registrationFormValidator->isEmailValid($request->request()->get('email'));
            $isPasswordConfValid=$registrationFormValidator->isInputValid("/^[A-Za-z0-9-!._]+$/",$request->request()->get('passwordConfirm'));
            $isEmailConfValid=$registrationFormValidator->isEmailValid($request->request()->get('emailConfirm'));
            $isEmailConfirmOk=$registrationFormValidator->isEqualToConfirm($request->request()->get('email'),$request->request()->get('emailConfirm'));
            $isPasswordConfirmOk=$registrationFormValidator->isEqualToConfirm($request->request()->get('password'),$request->request()->get('passwordConfirm'));
            $isEmailTaken=$this->userRepository->findOneBy(['email' => $request->request()->get('email')]);

            if ($isFirstnameValid
            && $isNameValid
            && $isPasswordValid
            && $isEmailValid
            && $isPasswordConfValid
            && $isEmailConfValid
            && $isEmailConfirmOk
            && $isPasswordConfirmOk
            && !$isEmailTaken)
            {
                $user = new User();
                $user->setName($request->request()->get('name'));
                $user->setFirstname($request->request()->get('firstname'));
                $user->setPassword($request->request()->get('password'));
                $user->setEmail($request->request()->get('email'));

                if ($this->userRepository->create($user))
                {
                    $this->session->set('user',$user);
                    $this->session->addFlashes('info','Inscription valide, votre compte avec l\'adresse Email : ' .$request->request()->get('email'). ' a bien été créé.');
                }
                else
                {
                    $this->session->addFlashes('error','Votre compte n\'a pas pu être créé.');
                }
                $response->redirect();
            }

            if (!$isFirstnameValid)
            {
                $this->session->addFlashes('error',"Votre prénom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
            }
            if (!$isNameValid)
            {
                $this->session->addFlashes('error',"Votre nom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
            }
            if (!$isPasswordValid || !$isPasswordConfValid)
            {
                $this->session->addFlashes('error',"Votre mot de passe ne peut pas contenir d'espaces ou caractères spéciaux exceptés ('-', '_', '!' et '.').");
            }
            if (!$isEmailValid || !$isEmailConfValid)
            {
                $this->session->addFlashes('error',"Votre email ne correspond pas.");
            }
            if (!$isEmailConfirmOk)
            {
                $this->session->addFlashes('error',"La confirmation de votre email ne correspond pas à votre email.");
            }
            if (!$isPasswordConfirmOk)
            {
                $this->session->addFlashes('error',"La confirmation de votre mot de passe ne correspond pas à votre mot de passe.");
            }
            if ($isEmailTaken)
            {
                $this->session->addFlashes('error',"Êtes-vous déjà inscrit(e)? Un compte avec cet email existe déjà.");
            }
            $response->redirect('?action=registration');
        }
            return $response;
    }
}