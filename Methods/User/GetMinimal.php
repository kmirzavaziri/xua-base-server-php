<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\CRUD\GetOneMethod;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array user
 * @method static MethodItemSignature R_user() The Signature of: Response Item `user`
 */
class GetMinimal extends GetOneMethod
{
    protected static function entityFields(): array
    {
        return [
            User::F_profilePicture(),
            User::F_firstNameFa(),
            User::F_lastNameFa()
        ];
    }

    protected function one(): Entity
    {
        $user = UserService::user();
        if (!$user->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.access.denied'));
        }
        return $user;
    }

    protected static function resultName(): string
    {
        return 'user';
    }
}