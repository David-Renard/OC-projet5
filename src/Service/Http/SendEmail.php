<?php

declare(strict_types=1);

namespace App\Service\Http;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Dotenv\Dotenv;
use App\Service\Environment as DotEnvironment;

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
        $dotenv = new Dotenv();
        $getEnv = new DotEnvironment($dotenv);

        $mail = new PHPMailer();
        $mail->isSMTP();

        $host = $getEnv->getEnv('MAIL_HOST');
        $port = $getEnv->getEnv('MAIL_PORT');
        $contactAddress = $getEnv->getEnv('MAIL_ADDRESS');

        $mail->Host = $host;
        $mail->Port = $port;
        $mail->CharSet = 'UTF-8';
//        $mail->SMTPDebug = 4;

        $mail->setFrom($from, 'contact');
        $mail->FromName = $fromName;
        $mail->addAddress($contactAddress,'Contact');

        $htmlBody = $this->twig->render("mail/contactmail.html.twig", [
            'subject' => $subject,
            'from' => $fromName,
            'message' => $body,
            ]);

        $mail->Subject = $subject;
        $mail->WordWrap = 100;
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        $mail->AltBody = $body;

        $mail->send();
    }
}

