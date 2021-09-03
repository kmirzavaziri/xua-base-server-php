<?php

namespace Methods\Farm\Owner;

use Entities\Farm;
use Entities\User;
use Methods\Abstraction\GetManyPager;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class GetMany extends GetManyPager
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_id(),
            User::F_titleFa(),
            User::F_profilePicture(),
        ]);
    }

    protected function condition(): Condition
    {
        return Condition::leaf(User::C_farms()->rel(Farm::C_id()), Condition::NISNULL);
    }
}