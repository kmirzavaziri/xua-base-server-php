<?php

namespace Methods\Farm;

use Entities\Farm;
use Entities\Farm\Field;
use Entities\Farm\FieldSignature;
use Entities\Farm\Media;
use Entities\Product;
use Entities\User;
use Methods\Abstraction\GetOneById;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\EntityInstantField;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class GetOne extends GetOneById
{
    protected static function entity(): string
    {
        return Farm::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Farm::F_title(),
            (new EntityFieldSignatureTree(Farm::F_gallery()))->addChildren([
                Media::F_id(),
                Media::F_source(),
            ]),
            Farm::F_averageAnnualInterest(),
            Farm::F_description(),
            Farm::F_story(),
            Farm::F_rate(),
            (new EntityFieldSignatureTree(Farm::F_owner()))->addChildren([
                User::F_id(),
                User::F_titleFa(),
            ]),
            new EntityInstantField('ostan', function (Farm $farm) { return $farm->ostan->title;}),
            new EntityInstantField('shahrestan', function (Farm $farm) { return $farm->shahrestan->title;}),
            new EntityInstantField('bakhsh', function (Farm $farm) { return $farm->bakhsh?->title;}),
            new EntityInstantField('dehestan', function (Farm $farm) { return $farm->dehestan?->title;}),
            (new EntityFieldSignatureTree(Farm::F_additionalFields()))->addChildren([
                (new EntityFieldSignatureTree(Field::F_fieldSignature()))->addChild(FieldSignature::F_title()),
                Field::F_value(),
            ]),
            (new EntityFieldSignatureTree(Farm::F_products()))->addChildren([
                Product::F_id(),
                Product::F_title(),
                Product::F_price(),
                Product::F_image(),
            ]),
        ]);
    }
}