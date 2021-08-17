<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use XUA\VARQUE\MethodView;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property mixed profilePicture
 * @method static MethodItemSignature R_profilePicture() The Signature of: Response Item `profilePicture`
 * @property ?string firstNameFa
 * @method static MethodItemSignature R_firstNameFa() The Signature of: Response Item `firstNameFa`
 * @property ?string lastNameFa
 * @method static MethodItemSignature R_lastNameFa() The Signature of: Response Item `lastNameFa`
 */
class GetMinimal extends MethodView
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            User::F_profilePicture(),
            User::F_firstNameFa(),
            User::F_lastNameFa()
        ];
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }
}