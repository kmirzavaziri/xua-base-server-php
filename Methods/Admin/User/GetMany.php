<?php

namespace Methods\Admin\User;

use Entities\User;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class GetMany extends GetManyPagerAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_id(),
            User::F_firstNameFa(),
            User::F_lastNameFa(),
            User::F_cellphoneNumber(),
            User::F_nationalCode(),
            User::F_email(),
        ]);
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