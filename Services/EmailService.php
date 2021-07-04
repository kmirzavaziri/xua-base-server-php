<?php

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Services\XUA\ConstantService;
use Services\XUA\ExpressionService;
use XUA\Service;

abstract class EmailService extends Service
{
    /**
     * @param EmailUser[] $receivers
     * @param string $subject
     * @param string $body
     * @param string|null $from
     * @param string|null $fromName
     */
    public static function send(
        array $receivers,
        string $subject,
        string $body,
        string $htmlBody = null,
        string $from = null,
        string $fromName = null,
    ) : void
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = ConstantService::get('config/email', 'smtp/host');
        $mail->SMTPAuth = true;
        $mail->Username = ConstantService::get('config/email', 'smtp/username');
        $mail->Password = ConstantService::get('config/email', 'smtp/password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = ConstantService::get('config/email', 'smtp/port');

        $mail->setFrom(
            ($from ?? 'support') . '@' . ConstantService::get('config/email', 'domain'),
            ($fromName ?? ExpressionService::get('email.from.name.support'))
        );

        foreach ($receivers as $receiver) {
            $mail->addAddress($receiver->address, $receiver->name);
        }

//        $mail->addReplyTo('info@example.com', 'Information');
//        $mail->addCC('cc@example.com');
//        $mail->addBCC('bcc@example.com');
//        $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        $mail->Subject = $subject;

        if ($htmlBody !== null) {
            $mail->isHTML(true);
            $mail->Body = $htmlBody;
            $mail->AltBody = $body;
        } else {
            $mail->Body = $body;
        }

        $mail->send();
    }
}