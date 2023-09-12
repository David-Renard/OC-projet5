<?php

declare(strict_types=1);

namespace App\Service\Http;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/PHPMailer/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/PHPMailer/src/SMTP.php';

class SendEmail
{
//    public function sendMail(string $from, string $fromName, string $subject, string $body) : bool
    public function sendMail(string $from, string $fromName, string $subject, string $body)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp-mail.outlook.com';
        $mail->SMTPAuth = 1;
        $mail->CharSet = 'UTF-8';
        $mail->Port = 587;
        $mail->SMTPDebug = 4;

        $mail->From = $from;
        $mail->FromName = $fromName;
        $mail->addAddress($from,'Contact');

        if($mail->SMTPAuth){
            $mail->SMTPSecure = 'tls';
            $mail->Username = $from;
            $mail->Password = 'xxxxxxx';
        }

        $mail->Subject = $subject;
        $mail->WordWrap = 100;
        $mail->isHTML(true);
        $mail->Body =
"<!DOCTYPE html>
<html>
    <div style='font-weight: bold'>Subject : $subject</div>
    <div style='font-style: italic'>From : $fromName</div>
    <div style='font-style: italic; font-size: 0.8rem'><p>Merci pour votre message, nous reviendrons vers vous dans les plus brefs délais.</p></div>
    <div style='font-style: italic'>Message : $body</div>
</html>";
        $mail->AltBody = $body;

        $mail->send();
//        return $mail->send();
    }
}

