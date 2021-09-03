<?php

namespace Methods\Admin\Farm;


use Entities\Farm;
use Entities\Farm\Field;
use Entities\Farm\Media;
use Entities\Product;
use Entities\User;
use Methods\Abstraction\GetOneByIdAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\EntityInstantField;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class GetOne extends GetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Farm::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Farm::F_title(),
            Farm::F_description(),
            Farm::F_story(),
            (new EntityFieldSignatureTree(Farm::F_additionalFields()))->addChildren([
                Field::F_id(),
                Field::F_fieldSignature(),
                Field::F_value(),
            ]),
            Farm::F_averageAnnualInterest(),
            Farm::F_rate(),
            Farm::F_status(),
            (new EntityFieldSignatureTree(Farm::F_gallery()))->addChildren([
                Media::F_id(),
                Media::F_source(),
            ]),
            (new EntityFieldSignatureTree(Farm::F_owner()))->addChildren([
                User::F_id(),
                User::F_titleFa(),
            ]),
            (new EntityFieldSignatureTree(Farm::F_products()))->addChildren([
                Product::F_id(),
                Product::F_title(),
            ]),
            new EntityInstantField('ostan', function (Farm $farm) { return $farm->ostan->id;}),
            new EntityInstantField('shahrestan', function (Farm $farm) { return $farm->shahrestan->id;}),
            new EntityInstantField('bakhsh', function (Farm $farm) { return $farm->bakhsh?->id;}),
            new EntityInstantField('dehestan', function (Farm $farm) { return $farm->dehestan?->id;}),
            Farm::F_address(),
            Farm::F_geolocationLat(),
            Farm::F_geolocationLong(),
            Farm::F_cooperationField(),
            Farm::F_proposal(),
        ]);
    }
}