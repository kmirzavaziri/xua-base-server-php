<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodAdjust;


/**
 * @property ?string Q_firstNameEn
 * @method static MethodItemSignature Q_firstNameEn() The Signature of: Request Item `firstNameEn`
 * @property ?string Q_lastNameEn
 * @method static MethodItemSignature Q_lastNameEn() The Signature of: Request Item `lastNameEn`
 * @property ?string Q_address
 * @method static MethodItemSignature Q_address() The Signature of: Request Item `address`
 * @property null|int|float Q_geolocationLat
 * @method static MethodItemSignature Q_geolocationLat() The Signature of: Request Item `geolocationLat`
 * @property null|int|float Q_geolocationLong
 * @method static MethodItemSignature Q_geolocationLong() The Signature of: Request Item `geolocationLong`
 * @property ?string Q_postalCode
 * @method static MethodItemSignature Q_postalCode() The Signature of: Request Item `postalCode`
 * @property ?string Q_landlinePhoneNumber
 * @method static MethodItemSignature Q_landlinePhoneNumber() The Signature of: Request Item `landlinePhoneNumber`
 * @property ?string Q_iban
 * @method static MethodItemSignature Q_iban() The Signature of: Request Item `iban`
 * @property ?string Q_nationality
 * @method static MethodItemSignature Q_nationality() The Signature of: Request Item `nationality`
 * @property null|\Services\XUA\DateTimeInstance Q_birthDate
 * @method static MethodItemSignature Q_birthDate() The Signature of: Request Item `birthDate`
 * @property ?string Q_gender
 * @method static MethodItemSignature Q_gender() The Signature of: Request Item `gender`
 * @property ?string Q_education
 * @method static MethodItemSignature Q_education() The Signature of: Request Item `education`
 * @property ?string Q_job
 * @method static MethodItemSignature Q_job() The Signature of: Request Item `job`
 * @property ?string Q_website
 * @method static MethodItemSignature Q_website() The Signature of: Request Item `website`
 * @property ?\Services\XUA\FileInstance Q_nationalCardPicture
 * @method static MethodItemSignature Q_nationalCardPicture() The Signature of: Request Item `nationalCardPicture`
 * @property ?\Services\XUA\FileInstance Q_idBookletPicture
 * @method static MethodItemSignature Q_idBookletPicture() The Signature of: Request Item `idBookletPicture`
 */
class SetNatural extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_firstNameEn(),
            User::F_lastNameEn(),
            User::F_address(),
            User::F_geolocationLat(),
            User::F_geolocationLong(),
            User::F_postalCode(),
            User::F_landlinePhoneNumber(),
            User::F_iban(),
            User::F_nationality(),
            User::F_birthDate(),
            User::F_gender(),
            User::F_education(),
            User::F_job(),
            User::F_website(),
            User::F_nationalCardPicture(),
            User::F_idBookletPicture(),
        ], false, null, false);
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }

    protected function validations(): void
    {
        $errorMessage = ExpressionService::get('errormessage.required.request.item.not.provided');
        if ($this->Q_firstNameEn === null) {
            $this->addAndThrowError('firstNameEn', $errorMessage);
        }
        if ($this->Q_lastNameEn === null) {
            $this->addAndThrowError('lastNameEn', $errorMessage);
        }
        if ($this->Q_address === null) {
            $this->addAndThrowError('address', $errorMessage);
        }
        if ($this->Q_geolocationLat === null) {
            $this->addAndThrowError('geolocationLat', $errorMessage);
        }
        if ($this->Q_geolocationLong === null) {
            $this->addAndThrowError('geolocationLong', $errorMessage);
        }
        if ($this->Q_postalCode === null) {
            $this->addAndThrowError('postalCode', $errorMessage);
        }
        if ($this->Q_nationalCardPicture === null) {
            $this->addAndThrowError('postalCode', $errorMessage);
        }
    }
}