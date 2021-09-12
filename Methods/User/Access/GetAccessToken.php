<?php

namespace Methods\User\Access;

use Entities\User;
use Entities\User\Session;
use Exception;
use Services\UserService;
use Services\XUA\ConstantService;
use Services\XUA\DateTimeInstance;
use Services\XUA\ExpressionService;
use Supers\Basics\Strings\Text;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property string Q_emailOrPhone
 * @method static MethodItemSignature Q_emailOrPhone() The Signature of: Request Item `emailOrPhone`
 * @property string Q_verificationCode
 * @method static MethodItemSignature Q_verificationCode() The Signature of: Request Item `verificationCode`
 * @property string accessToken
 * @method static MethodItemSignature R_accessToken() The Signature of: Response Item `accessToken`
 */
class GetAccessToken extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'emailOrPhone' => new MethodItemSignature(new Text([]), true, null, false),
            'verificationCode' => new MethodItemSignature(Session::F_verificationCode()->type, true, null, false),
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            'accessToken' => new MethodItemSignature(new Text([]), true, null, false),
        ]);
    }

    protected function body(): void
    {
        $emailOrPhone = $this->Q_emailOrPhone;
        try {
            $user = UserService::getUserByEmailOrPhone($emailOrPhone, $isEmail);
        } catch (Exception $e) {
            $this->addAndThrowError('emailOrPhone', $e->getMessage());
        }
        if (!$user->id) {
            $this->addAndThrowError('emailOrPhone', ExpressionService::get('errormessage.email.or.cellphone.is.not.valid'));
        }

        $session = Session::getOne(
            Condition::leaf(Session::C_user()->rel(User::C_id()), Condition::EQ, $user->id)
                ->and(Session::C_verificationCode(), Condition::EQ, $this->Q_verificationCode)
                ->and(Session::C_accessToken(), Condition::EQ, '')
                ->and(Session::C_codeSentAt(), Condition::GRATER, (new DateTimeInstance())->dist(new DateTimeInstance(ConstantService::VERIFICATION_CODE_EXPIRATION_TIME)))
        );
        if (!$session->id) {
            $this->addAndThrowError('emailOrPhone', ExpressionService::get('errormessage.verification.code.is.invalid'));
        }

        $session->verificationCode = null;
        $session->accessToken = UserService::generateAccessToken($session);
        $session->store();

        $this->accessToken = $session->user->id . ':' . $session->accessToken;
    }
}