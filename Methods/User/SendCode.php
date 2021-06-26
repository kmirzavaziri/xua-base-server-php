<?php

namespace Methods\User;

use Entities\User;
use Entities\User\Session;
use Services\EmailService;
use Services\SmsService;
use Services\UserService;
use Services\XUA\DateTimeInstance;
use Services\XUA\ExpressionService;
use Supers\Basics\Strings\Text;
use Supers\Customs\Email;
use Supers\Customs\IranPhone;
use XUA\Exceptions\MethodRequestException;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property string Q_emailOrPhone
 * @method static MethodItemSignature Q_emailOrPhone() The Signature of: Request Item `emailOrPhone`
 */
class SendCode extends Method
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
        $cellphoneType = new IranPhone(['type' => 'cellphone']);
        $EmailType = new Email([]);
        $isEmail = false;
        if ($cellphoneType->accepts($emailOrPhone)) {
            $condition = Condition::leaf(User::C_cellphoneNumber(), Condition::EQ, $emailOrPhone);
        } elseif ($EmailType->accepts($emailOrPhone)) {
            $condition = Condition::leaf(User::C_email(), Condition::EQ, $emailOrPhone);
            $isEmail = true;
        } else {
            throw (new MethodRequestException)->setError('emailOrPhone', ExpressionService::get('email.or.cellphone.is.not.valid'));
        }

        $user = User::getOne($condition);
        if (!$user->id) {
            if ($isEmail) {
                $user->email = $emailOrPhone;
            } else {
                $user->cellphoneNumber = $emailOrPhone;
            }
            $user->store();
        }

        $session = new Session();
        $session->user = $user;
        $session->code = UserService::generateCode();
        $session->codeSentAt = new DateTimeInstance();
        $session->codeSentVia = $isEmail ? 'email' : 'sms';
        $session->store();

        if ($isEmail) {
            EmailService::send(
                $user->email,
                ExpressionService::get('verification.code'),
                ExpressionService::get('your.code.is.code', ['code' => $session->code])
            );
        } else {
            SmsService::send(
                $user->cellphoneNumber,
                ExpressionService::get('your.code.is.code', ['code' => $session->code])
            );
        }
    }
}