<?php

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use Services\XUA\ConstantService;
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
        string $from = null,
        string $fromName = null,
    ) : void
    {
        $mail = new PHPMailer(true);

        $from = $from ?? 'support';
        $fromName = ConstantService::get('config/XUA/general', 'title') . ($fromName ?? 'Support');

        $from = $from . '@' . ConstantService::get('config/XUA/general', 'url');

        $mail->setFrom($from, $fromName);

        foreach ($receivers as $receiver) {
            $mail->addAddress($receiver->address, $receiver->name);
        }

//        $mail->addReplyTo('info@example.com', 'Information');
//        $mail->addCC('cc@example.com');
//        $mail->addBCC('bcc@example.com');
//        $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
//        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;
//        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
    }
}