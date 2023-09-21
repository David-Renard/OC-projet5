<?php

declare(strict_types=1);

namespace App\Service\Http;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require '../vendor/PHPMailer/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/PHPMailer/src/SMTP.php';

class SendEmail
{
    private Environment $twig;
    public function __construct()
    {
        $loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($loader);
    }

    public function sendMail(string $from, string $fromName, string $subject, string $body): void
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
//        $mail->Host = 'smtp-mail.outlook.com';
        $mail->Host = 'localhost';
//        $mail->SMTPAuth = 1;
        $mail->CharSet = 'UTF-8';
//        $mail->Port = 587;
        $mail->Port = 1025;
        $mail->SMTPDebug = 4;

        $mail->setFrom($from, 'contact');
        $mail->FromName = $fromName;
        $mail->addAddress('contact_davidr@gmail.com','Contact');

//        if($mail->SMTPAuth){
//            $mail->SMTPSecure = 'tls';
//            $mail->Username = $from;
//            $mail->Password = 'david365+';
//        }


        $htmlBody = $this->twig->render("mail/contactmail.html.twig", [
            'subject' => $subject,
            'from' => $fromName,
            'message' => $body,
            ]);

        $mail->Subject = $subject;
        $mail->WordWrap = 100;
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        $mail->AltBody = $htmlBody;

        $mail->send();
    }
}

