<?php

namespace Methods\User\Session;

use Entities\User;
use Entities\User\Session;
use Services\UserService;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodQuery;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array sessions
 * @method static MethodItemSignature R_sessions() The Signature of: Response Item `sessions`
 */
class GetAll extends MethodQuery
{
    protected static function entity(): string
    {
        return Session::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Session::F_lastOnline(),
            Session::F_ip(),
            Session::F_location(),
            Session::F_systemInfo()
        ]);
    }

    protected function condition(): Condition
    {
        $user = UserService::verifyUser($this->error);
        return Condition::leaf(Session::C_user()->rel(User::C_id()), Condition::EQ, $user->id)
            ->and(Session::C_accessToken(), Condition::NEQ, '')
        ;
    }

    protected static function wrapper(): string
    {
        return 'sessions';
    }
}