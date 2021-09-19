<?php

namespace Methods\Extra\WorkWithUs\FarmOwner\Farm;

use Entities\Farm;
use Entities\Farm\Field;
use Entities\User;
use Services\UserService;
use XUA\Entity;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodAdjust;

/**
 * @property string Q_title
 * @method static MethodItemSignature Q_title() The Signature of: Request Item `title`
 * @property string Q_description
 * @method static MethodItemSignature Q_description() The Signature of: Request Item `description`
 * @property ?string Q_story
 * @method static MethodItemSignature Q_story() The Signature of: Request Item `story`
 * @property array Q_additionalFields
 * @method static MethodItemSignature Q_additionalFields() The Signature of: Request Item `additionalFields`
 * @property int|float Q_averageAnnualInterest
 * @method static MethodItemSignature Q_averageAnnualInterest() The Signature of: Request Item `averageAnnualInterest`
 * @property string Q_agentType
 * @method static MethodItemSignature Q_agentType() The Signature of: Request Item `agentType`
 * @property string Q_ownership
 * @method static MethodItemSignature Q_ownership() The Signature of: Request Item `ownership`
 * @property ?\Services\XUA\FileInstance Q_agreementPicture
 * @method static MethodItemSignature Q_agreementPicture() The Signature of: Request Item `agreementPicture`
 * @property ?array Q_deedDetails
 * @method static MethodItemSignature Q_deedDetails() The Signature of: Request Item `deedDetails`
 * @property int Q_geographicDivision
 * @method static MethodItemSignature Q_geographicDivision() The Signature of: Request Item `geographicDivision`
 * @property ?string Q_address
 * @method static MethodItemSignature Q_address() The Signature of: Request Item `address`
 * @property null|int|float Q_geolocationLat
 * @method static MethodItemSignature Q_geolocationLat() The Signature of: Request Item `geolocationLat`
 * @property null|int|float Q_geolocationLong
 * @method static MethodItemSignature Q_geolocationLong() The Signature of: Request Item `geolocationLong`
 * @property ?string Q_cooperationField
 * @method static MethodItemSignature Q_cooperationField() The Signature of: Request Item `cooperationField`
 * @property ?\Services\XUA\FileInstance Q_proposal
 * @method static MethodItemSignature Q_proposal() The Signature of: Request Item `proposal`
 */
class SetOne extends MethodAdjust
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
            Farm::F_geographicDivision(),
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
        $farm = Farm::getOne(Condition::leaf(Farm::C_agent()->rel(User::C_id()), Condition::EQ, $user->id));
        $farm->agent = $user;
        return $farm;
    }
}