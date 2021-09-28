<?php

namespace Methods\Extra\WorkWithUs\FarmOwner\Farm;

use Entities\Farm;
use Entities\Farm\Field;
use Entities\User;
use Services\UserService;
use XUA\Entity;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\EntityInstantField;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodView;

/**
 * @property ?string title
 * @method static MethodItemSignature R_title() The Signature of: Response Item `title`
 * @property ?string description
 * @method static MethodItemSignature R_description() The Signature of: Response Item `description`
 * @property ?string story
 * @method static MethodItemSignature R_story() The Signature of: Response Item `story`
 * @property ?array additionalFields
 * @method static MethodItemSignature R_additionalFields() The Signature of: Response Item `additionalFields`
 * @property null|int|float averageAnnualInterest
 * @method static MethodItemSignature R_averageAnnualInterest() The Signature of: Response Item `averageAnnualInterest`
 * @property ?string agentType
 * @method static MethodItemSignature R_agentType() The Signature of: Response Item `agentType`
 * @property ?string ownership
 * @method static MethodItemSignature R_ownership() The Signature of: Response Item `ownership`
 * @property ?\Services\XUA\FileInstance agreementPicture
 * @method static MethodItemSignature R_agreementPicture() The Signature of: Response Item `agreementPicture`
 * @property ?array deedDetails
 * @method static MethodItemSignature R_deedDetails() The Signature of: Response Item `deedDetails`
 * @property mixed ostan
 * @method static MethodItemSignature R_ostan() The Signature of: Response Item `ostan`
 * @property mixed shahrestan
 * @method static MethodItemSignature R_shahrestan() The Signature of: Response Item `shahrestan`
 * @property mixed bakhsh
 * @method static MethodItemSignature R_bakhsh() The Signature of: Response Item `bakhsh`
 * @property mixed dehestan
 * @method static MethodItemSignature R_dehestan() The Signature of: Response Item `dehestan`
 * @property mixed shahrRoosta
 * @method static MethodItemSignature R_shahrRoosta() The Signature of: Response Item `shahrRoosta`
 * @property ?string address
 * @method static MethodItemSignature R_address() The Signature of: Response Item `address`
 * @property null|int|float geolocationLat
 * @method static MethodItemSignature R_geolocationLat() The Signature of: Response Item `geolocationLat`
 * @property null|int|float geolocationLong
 * @method static MethodItemSignature R_geolocationLong() The Signature of: Response Item `geolocationLong`
 * @property ?string cooperationField
 * @method static MethodItemSignature R_cooperationField() The Signature of: Response Item `cooperationField`
 * @property ?\Services\XUA\FileInstance proposal
 * @method static MethodItemSignature R_proposal() The Signature of: Response Item `proposal`
 */
class GetOne extends MethodView
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
            Farm::F_agentType(),
            Farm::F_ownership(),
            Farm::F_agreementPicture(),
            Farm::F_deedDetails(),
            new EntityInstantField('ostan', function (Farm $farm) { return $farm->ostan?->id;}),
            new EntityInstantField('shahrestan', function (Farm $farm) { return $farm->shahrestan?->id;}),
            new EntityInstantField('bakhsh', function (Farm $farm) { return $farm->bakhsh?->id;}),
            new EntityInstantField('dehestan', function (Farm $farm) { return $farm->dehestan?->id;}),
            new EntityInstantField('shahrRoosta', function (Farm $farm) { return $farm->shahrRoosta?->id;}),
            Farm::F_address(),
            Farm::F_geolocationLat(),
            Farm::F_geolocationLong(),
            Farm::F_cooperationField(),
            Farm::F_proposal(),
        ]);
    }

    protected function feed(): Entity
    {
        $user = UserService::verifyUser($this->error);
        return Farm::getOne(Condition::leaf(Farm::C_agent()->rel(User::C_id()), Condition::EQ, $user->id));
    }
}