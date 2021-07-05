<?php

namespace Methods\User;

use Entities\User;
use Entities\User\Session;
use Services\EmailService;
use Services\EmailUser;
use Services\SmsService;
use Services\UserService;
use Services\XUA\DateTimeInstance;
use Services\XUA\ExpressionService;
use Services\XUA\TemplateService;
use Supers\Basics\Strings\Text;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property string Q_emailOrPhone
 * @method static MethodItemSignature Q_emailOrPhone() The Signature of: Request Item `emailOrPhone`
 */
class SendVerificationCode extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'emailOrPhone' => new MethodItemSignature(new Text([]), true, null, false),
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [

        ]);
    }

    protected function execute(): void
    {
        $emailOrPhone = $this->Q_emailOrPhone;
        $user = UserService::getUserByEmailOrPhone($emailOrPhone, $isEmail);
        if (!$user->id) {
            if ($isEmail) {
                $user->email = $emailOrPhone;
            } else {
                $user->cellphoneNumber = $emailOrPhone;
            }
            $user->store();
        }

        $secondsAgo = (new DateTimeInstance())->dist(new DateTimeInstance(UserService::VERIFICATION_CODE_EXPIRATION_TIME));
        $session = Session::getOne(
            Condition::leaf(Session::C_user()->rel(User::C_id()), Condition::EQ, $user->id)
                ->and(Session::C_codeSentAt(), Condition::GRATER, $secondsAgo)
        );
        if ($session->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.wait.seconds.to.send.verification.code', ['seconds' => $session->codeSentAt->dist($secondsAgo)->getTimestamp()]));
        }


        $verificationCode = UserService::generateVerificationCode();

        if ($isEmail) {
            EmailService::send(
                [new EmailUser($user->email)],
                ExpressionService::get('verification.code'),
                ExpressionService::get('your.verification.code.is.code', ['verificationCode' => $verificationCode]),
                TemplateService::render('emails/verificationCodeEmail.twig', ['verificationCode' => $verificationCode]),
                'hello',
                ExpressionService::get('verification.code.email.from.name'),
            );
        } else {
            SmsService::sendTemplate(
                $user->cellphoneNumber,
                SmsService::SMS_IR_VERIFICATION_TEMPLATE_ID,
                ['VerificationCode' => $verificationCode]
            );
        }

        $session = new Session();
        $session->user = $user;
        $session->verificationCode = $verificationCode;
        $session->codeSentAt = new DateTimeInstance();
        $session->codeSentVia = $isEmail ? 'email' : 'sms';
        $session->store();
    }
}