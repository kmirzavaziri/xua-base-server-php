<?php

namespace Methods\User\Session;

use Entities\User;
use Entities\User\Session;
use Services\UserService;

use XUA\ReadMethod;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array sessions
 * @method static MethodItemSignature R_sessions() The Signature of: Response Item `sessions`
 */
class GetAll extends ReadMethod
{
    protected static function resultName(): string
    {
        return 'sessions';
    }

    protected static function entityFields(): array
    {
        return [
            Session::F_id(),
            Session::F_lastOnline(),
            Session::F_ip(),
            Session::F_location(),
            Session::F_systemInfo()
        ];
    }

    protected function entityItems(): array
    {
        $user = UserService::user();
        if (!$user->id) {
            $this->addAndThrowError('', 'errormessage.access.denied');
        }
        return Session::getMany(
            Condition::leaf(Session::C_user()->rel(User::C_id()), Condition::EQ, $user->id)
                ->and(Session::C_accessToken(), Condition::NEQ, '')
        );
    }
}