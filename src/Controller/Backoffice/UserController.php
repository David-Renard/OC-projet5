<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Service\FormValidator\LoginFormValidator;
use App\View\View;
use App\Service\Http\Request;
use App\Service\Http\Response;
use App\Model\Repository\UserRepository;
use App\Model\Entity\User;
use App\Service\Http\Session\Session;

class UserController
{
    public function __construct(private UserRepository $userRepository, private View $view, private Session $session)
    {
    }

    public function displayHomeAdmin(Request $request): Response
    {
        $loginFormValidator = new LoginFormValidator($request, $this->userRepository, $this->session);
        $authorizationLevel = $loginFormValidator->isAuthorized();
        if ($authorizationLevel === 'unauthorized')
        {
            $this->session->addFlashes("error","Vous ne disposez pas des droits d'accès à cette partie du site.");
        }
        elseif ($authorizationLevel === 'authorized')
        {
            $this->session->addFlashes('success','Bienvenue sur la partie administration du site');
        }
        else
        {
            $this->session->addFlashes("error","Vous devez être connecté et disposer des droits d'accès pour accéder à cette partie du site.");
        }

        return new Response($this->view->render(
            [
                "template" => 'admin',
                'office' => 'backoffice',
            ]));
    }

    public function displayUsers(Request $request, UserRepository $userRepository): Response
    {
        $users=$this->userRepository->findAll();
        $roleArray=['super_admin','admin','user'];
        $response = new Response($this->view->render(
            [
                "template" => 'adminusers',
                'data' => [
                    'users' => $users,
                    'rolearray' => $roleArray,
                ],
                'office' => 'backoffice',
            ]));

        if ($request->getMethod() === 'POST')
        {
            $userRole = $request->request()->get('role');
            if ($userRole === null)
            {
                $this->session->addFlashes('info', 'Il n\'y a pas d\'utilisateur dont le role a changé.');
            }
            else
            {
                foreach ($userRole as $key => $role)
                {
                    $user = new User();
                    $user->setRole($role);
                    $user->setId($key);
//                    $user->setName($this->session->get('user')->getName());
//                    $user->setFirstname($this->session->get('user')->getFirstname());
//                    $user->setEMail($this->session->get('user')->getEmail());
//                    ?><!--<pre>--><?php
//                    var_dump($user);die;
//                    ?><!--<pre>--><?php
                    $userRepository->update($user);
                }
                $this->session->addFlashes('success', 'Les rôles ont été modifié avec succès.');
                $response->redirect('?action=adminusers');
            }

//            $userToDelete = $request->request()->get('states');
//            if ($userToDelete === null)
//            {
//                $this->session->addFlashes('info', 'Il n\'y a pas d\'utilisateur supprimé.');
//            }
//            else
//            {
//                foreach ($userToDelete as $key => $states)
//                {
//                    $user = new User();
//                    $user->setRole($this->session->get('user')->getRole());
//                    $user->setId($key);
//                    $user->setName('anonyme');
//                    $user->setFirstname('Utilisateur');
//                    $user->setEMail('utilisateur anonyme');
//                    $userRepository->update($user);
//                }
//                $this->session->addFlashes('success', 'Les utilisateurs sélectionnés ont été supprimé avec succès.');
////                $response->redirect('?action=adminusers');
//            }
        }
//            ?><!--<pre>--><?php
//            var_dump($userRole,$userToDelete);die;
//            ?><!--<pre>--><?php
        return $response;
    }
}