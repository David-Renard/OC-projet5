<?php
declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Model\Entity\User;
use App\Service\FormValidator\InputFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Service\Http\Session\Session;
use App\Service\Token;
use App\Model\Repository\UserRepository;
use App\Service\FormValidator\LoginFormValidator;

class UserController
{
    public function __construct(private UserRepository $userRepository, private View $view, private Session $session)
    {
    }

    /**
     * login action with token verification
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        $response = new Response();

        if ($this->session->get('user')) {
            $response->redirect();
        }

        $token = new Token($this->session);

        if ($request->getMethod() === 'GET') {
            $token->setToken();
        } elseif ($request->getMethod() === 'POST') {
            if ($token->verifyToken($request)) {
                $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
                $isLoginFormValid = $loginFormValidator->isValid();
                if ($isLoginFormValid) {
                    $this->session->addFlashes('info', "Bonjour et bienvenue {$this->session->get("user")->getFirstname()} !");
                    $response->redirect();
                } else {
                    $this->session->addFlashes('error', 'Mauvais identifiant(s)');
                    $response->redirect('?action=login');
                }
            } elseif ($token->verifyToken($request) === false) {
                $this->session->addFlashes('error', 'Il semblerait que ce ne soit pas vous qui tentez de vous connecter!?');
                $response->redirect('?action=login');
            }
        }

        return new Response($this->view->render([
            'template' => 'login',
            "office" => 'frontoffice',
        ]));
    }

    public function logoutAction(): Response
    {
        $this->session->remove('user');
        $this->session->remove('token');

        $response = new Response($this->view->render([
            'template' => 'home',
            "office" => 'frontoffice',
        ]));
        $response->redirect();
        return $response;
    }

    public function registration(Request $request, array $inputs = []): Response
    {
        $response = new Response();

        if ($this->session->get('user')) {
            $response->redirect();
        }

        $token = new Token($this->session);
        if ($request->getMethod() === 'GET') {
            $token->setToken();
        } elseif ($request->getMethod() === 'POST') {
            if ($token->verifyToken($request)) {
                $firstname = $request->request()->get('firstname');
                $name = $request->request()->get('name');
                $email = $request->request()->get('email');
                $password = $request->request()->get('password');
                $confEmail = $request->request()->get('emailConfirm');
                $confPassword = $request->request()->get('passwordConfirm');

                $registrationFormValidator = new InputFormValidator($request);
                $isFirstnameValid = $registrationFormValidator->isInputValid("/^[A-Za-z- _]+$/", $firstname);
                $isNameValid = $registrationFormValidator->isInputValid("/^[A-Za-z- _]+$/", $name);
                $isPasswordValid = $registrationFormValidator->isInputValid("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^<>&*-]).{8,}$/", $password);
                $isEmailValid = $registrationFormValidator->isEmailValid($email);
                $isPasswordConfValid = $registrationFormValidator->isInputValid("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^<>&*-]).{8,}$/", $confPassword);
                $isEmailConfValid = $registrationFormValidator->isEmailValid($confEmail);
                $isEmailConfirmOk = $registrationFormValidator->isEqualToConfirm($email, $confEmail);
                $isPasswordConfirmOk = $registrationFormValidator->isEqualToConfirm($password, $confPassword);
                $isEmailTaken = $this->userRepository->findOneBy(['email' => $email]);

                if ($isFirstnameValid
                    && $isNameValid
                    && $isPasswordValid
                    && $isEmailValid
                    && $isPasswordConfValid
                    && $isEmailConfValid
                    && $isEmailConfirmOk
                    && $isPasswordConfirmOk
                    && $isEmailTaken === false
                ) {
                    $user = new User();
                    $user->setName($name);
                    $user->setFirstname($firstname);
                    $user->setPassword(password_hash($request->request()->get('password'), PASSWORD_BCRYPT));
                    $user->setEmail($email);

                    if ($this->userRepository->create($user)) {
                        $this->session->addFlashes('info', 'Inscription valide, votre compte avec l\'adresse Email : ' . $email . ' a bien été créé. Vous pouvez dès maintenant vous connecter.');
                    } else {
                        $this->session->addFlashes('error', 'Votre compte n\'a pas pu être créé.');
                    }
                    $response->redirect();
                } elseif (empty($inputs)) {
                    if ($registrationFormValidator->isEmpty($name)) {
                        $this->session->addFlashes('error', 'Vous n\'avez pas saisi de nom.');
                    }
                    if ($registrationFormValidator->isEmpty($firstname)) {
                        $this->session->addFlashes('error', 'Vous n\'avez pas saisi de prénom.');
                    }
                    if ($registrationFormValidator->isEmpty($email)) {
                        $this->session->addFlashes('error', 'Vous n\'avez pas saisi d\'adresse email.');
                    }
                    if ($registrationFormValidator->isEmpty($password)) {
                        $this->session->addFlashes('error', 'Vous n\'avez pas saisi de mot de passe.');
                    }
                    if ($isFirstnameValid === false) {
                        $this->session->addFlashes('error', "Votre prénom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
                    }
                    if ($isNameValid === false) {
                        $this->session->addFlashes('error', "Votre nom ne peut pas contenir de caractères numériques ou autres caractères spéciaux (exceptés ' ', '-' et '_').");
                    }
                    if ($isPasswordValid === false
                        || $isPasswordConfValid === false
                    ) {
                        $this->session->addFlashes('error', "Votre mot de passe doit avoir au moins 8 caractères, 1 minuscule, 1 majuscule, 1 chiffre et 1 caractère spécial.");
                    }
                    if ($isEmailValid === false
                        || $isEmailConfValid === false
                    ) {
                        $this->session->addFlashes('error', "Votre email ne correspond pas.");
                    }
                    if ($isEmailConfirmOk === false) {
                        $this->session->addFlashes('error', "La confirmation de votre email ne correspond pas à votre email.");
                    }
                    if ($isPasswordConfirmOk === false) {
                        $this->session->addFlashes('error', "La confirmation de votre mot de passe ne correspond pas à votre mot de passe.");
                    }
                    if ($isEmailTaken) {
                        $this->session->addFlashes('error', "Êtes-vous déjà inscrit(e)? Un compte avec cet email existe déjà.");
                    }
                    $inputs = [
                        'name' => $name,
                        'firstname' => $firstname,
                        'email' => $email,
                    ];
                    return $this->registration($request, $inputs);
                }
            } else {
                $this->session->addFlashes('error', 'Il semblerait que ce ne soit pas vous qui tentez de vous inscrire!?');
                $response->redirect('?action=registration');
            }
        }
        return new Response($this->view->render([
            'template' => 'registration',
            'office' => 'frontoffice',
            'data' => ['inputs' => $inputs,]
        ]));
    }
}