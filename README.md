![Alt text](public/favicon.ico)
# OC - projet 5 : Votre premier blog PHP
## Parcours : Développeur d'application - PHP/Symfony

## Codacy
Here is the review of code by Codacy (on develop branch): [![Codacy Badge](https://app.codacy.com/project/badge/Grade/ec77af334aeb420f86577fe842d8e995)](https://app.codacy.com/gh/David-Renard/OC-projet5/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Présentation du projet
* Blog PHP programmé orienté objet
* Architecture en MVC
* Librairies externes autorisées uniquement si installées avec Composer
* Utilisation de Twig comme moteur de templating
* Partie publique :
  * Accueil :
    * Présentation,
    * Formulaire de contact.
  * Page des posts :
    * Ensemble des posts publiés (avec pagination),
    * Détail du post.
  * Page de détail d'un post :
    * Ensemble des éléments d'un post,
    * Formulaire d'ajout de commentaires (si utilisateur connecté).
  * Connexion (si utilisateur non connecté)
  * Déconnexion (si utilisateur connecté)
  * Inscription
* Partie administration :
  * Gestion des posts :
    * Suppression,
    * Modification (des éléments du posts et/ou de l'auteur),
    * Ajout.
  * Gestion des commentaires :
    * Modération des commentaires (par post).
  * Gestion des utilisateurs (accessible uniquement pour le super-admin) :
    * Changement de rôle d'un utilisateur,
    * Suppression d'un utilisateur.
* Gestion de la sécurité

## Installation
1. Clone the repository (https://github.com/David-Renard/OC-projet5.git).
2. Download and install one of the following servers :
   * laragon : https://laragon.org/download/index.html
   * xampp : https://www.wampserver.com/en/download-wampserver-64bits/
   * mamp : https://www.mamp.info/en/downloads/
3. Take care about getting php v8.2.9.
4. On your own server, create a new database named "blog".
5. On this local server, import the db.sql file located in this repository in the "database" directory.
6. The user is "root" and the password is "".
7. You have now to install composer at the root of this project by typing the command "composer install".
8. Your project should now be ready !
