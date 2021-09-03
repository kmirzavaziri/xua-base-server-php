<?php

namespace Methods\Farm;

use Entities\Farm;
use Entities\User;
use Methods\Abstraction\GetManyPager;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class GetMany extends GetManyPager
{
    protected static function entity(): string
    {
        return Farm::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Farm::F_id(),
            Farm::F_image(),
            Farm::F_title(),
            (new EntityFieldSignatureTree(Farm::F_owner()))->addChild(User::F_titleFa()),
            Farm::F_averageAnnualInterest(),
            Farm::F_description(),
            Farm::F_story(),
            Farm::F_rate(),
        ]);
    }
}