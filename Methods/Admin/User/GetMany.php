<?php

namespace Methods\Admin\User;

use Entities\User;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Signature\EntityFieldSignature;

class GetMany extends GetManyPagerAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            User::F_id(),
            User::F_profilePicture(),
            User::F_firstNameFa(),
            User::F_lastNameFa()
        ];
    }

    protected static function wrapper(): string
    {
        return 'users';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return null;
    }

}