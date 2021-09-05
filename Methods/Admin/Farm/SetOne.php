<?php

namespace Methods\Admin\Farm;

use Entities\Farm;
use Entities\Farm\Field;
use Methods\Abstraction\SetOneByIdAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class SetOne extends SetOneByIdAdmin
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
            Farm::F_status(),
            Farm::F_agent(),
            Farm::F_agentType(),
            Farm::F_products(),
            Farm::F_geographicDivision(),
            Farm::F_address(),
            Farm::F_geolocationLat(),
            Farm::F_geolocationLong(),
            Farm::F_cooperationField(),
        ], false);
    }
}