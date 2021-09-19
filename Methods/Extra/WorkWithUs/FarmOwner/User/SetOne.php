<?php

namespace Methods\Extra\WorkWithUs\FarmOwner\User;

use Entities\User;
use Entities\User\Info\FarmOwner;
use Services\UserService;
use Services\XUA\ExpressionService;
use Services\XUA\FileInstanceSame;
use XUA\Entity;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodAdjust;

/**
 * @property ?string Q_nationality
 * @method static MethodItemSignature Q_nationality() The Signature of: Request Item `nationality`
 * @property null|\Services\XUA\DateTimeInstance Q_birthDate
 * @method static MethodItemSignature Q_birthDate() The Signature of: Request Item `birthDate`
 * @property ?string Q_gender
 * @method static MethodItemSignature Q_gender() The Signature of: Request Item `gender`
 * @property ?string Q_nationalCode
 * @method static MethodItemSignature Q_nationalCode() The Signature of: Request Item `nationalCode`
 * @property ?string Q_firstNameFa
 * @method static MethodItemSignature Q_firstNameFa() The Signature of: Request Item `firstNameFa`
 * @property ?string Q_lastNameFa
 * @method static MethodItemSignature Q_lastNameFa() The Signature of: Request Item `lastNameFa`
 * @property ?string Q_faxNumber
 * @method static MethodItemSignature Q_faxNumber() The Signature of: Request Item `faxNumber`
 * @property ?string Q_website
 * @method static MethodItemSignature Q_website() The Signature of: Request Item `website`
 * @property ?string Q_email
 * @method static MethodItemSignature Q_email() The Signature of: Request Item `email`
 * @property ?string Q_iban
 * @method static MethodItemSignature Q_iban() The Signature of: Request Item `iban`
 * @property ?string Q_address
 * @method static MethodItemSignature Q_address() The Signature of: Request Item `address`
 * @property ?string Q_postalCode
 * @method static MethodItemSignature Q_postalCode() The Signature of: Request Item `postalCode`
 * @property ?\Services\XUA\FileInstance Q_profilePicture
 * @method static MethodItemSignature Q_profilePicture() The Signature of: Request Item `profilePicture`
 * @property ?string Q_firstNameEn
 * @method static MethodItemSignature Q_firstNameEn() The Signature of: Request Item `firstNameEn`
 * @property ?string Q_lastNameEn
 * @method static MethodItemSignature Q_lastNameEn() The Signature of: Request Item `lastNameEn`
 * @property ?string Q_cellphoneNumber
 * @method static MethodItemSignature Q_cellphoneNumber() The Signature of: Request Item `cellphoneNumber`
 * @property ?string Q_landlinePhoneNumber
 * @method static MethodItemSignature Q_landlinePhoneNumber() The Signature of: Request Item `landlinePhoneNumber`
 * @property ?string Q_referralMethod
 * @method static MethodItemSignature Q_referralMethod() The Signature of: Request Item `referralMethod`
 * @property ?string Q_referralDetails
 * @method static MethodItemSignature Q_referralDetails() The Signature of: Request Item `referralDetails`
 * @property ?\Services\XUA\FileInstance Q_nationalCardPicture
 * @method static MethodItemSignature Q_nationalCardPicture() The Signature of: Request Item `nationalCardPicture`
 * @property ?\Services\XUA\FileInstance Q_idBookletPicture
 * @method static MethodItemSignature Q_idBookletPicture() The Signature of: Request Item `idBookletPicture`
 * @property ?string Q_bio
 * @method static MethodItemSignature Q_bio() The Signature of: Request Item `bio`
 * @property ?array Q_infoFarmOwner
 * @method static MethodItemSignature Q_infoFarmOwner() The Signature of: Request Item `infoFarmOwner`
 */
class SetOne extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_nationality(),
            User::F_birthDate(),
            User::F_gender(),
            User::F_nationalCode(),
            User::F_firstNameFa(),
            User::F_lastNameFa(),
            User::F_faxNumber(),
            User::F_website(),
            User::F_email(),
            User::F_iban(),
            User::F_address(),
            User::F_postalCode(),
            User::F_profilePicture(),
            User::F_firstNameEn(),
            User::F_lastNameEn(),
            User::F_cellphoneNumber(),
            User::F_landlinePhoneNumber(),
            User::F_referralMethod(),
            User::F_referralDetails(),
            User::F_nationalCardPicture(),
            User::F_idBookletPicture(),
            User::F_bio(),
            (new EntityFieldSignatureTree(User::F_infoFarmOwner()))->addChildren([
                FarmOwner::F_bankAccountType(),
                FarmOwner::F_emergencyPhoneNumber(),
                FarmOwner::F_skills(),
                FarmOwner::F_experiences(),
            ]),
        ]);
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }

    protected function validations(): void
    {
        $errorMessage = ExpressionService::get('errormessage.required.request.item.not.provided');
        if ($this->Q_firstNameFa === null) {
            $this->addAndThrowError('firstNameFa', $errorMessage);
        }
        if ($this->Q_lastNameFa === null) {
            $this->addAndThrowError('lastNameFa', $errorMessage);
        }
        if ($this->Q_bio === null) {
            $this->addAndThrowError('bio', $errorMessage);
        }
        if ($this->Q_nationalCode === null) {
            $this->addAndThrowError('nationalCode', $errorMessage);
        }
        if ($this->Q_profilePicture === null or
            (is_a($this->Q_profilePicture, FileInstanceSame::class) and UserService::user()->profilePicture === null)) {
            $this->addAndThrowError('profilePicture', $errorMessage);
        }
        if ($this->Q_nationalCardPicture === null or
            (is_a($this->Q_nationalCardPicture, FileInstanceSame::class) and UserService::user()->nationalCardPicture === null)) {
            $this->addAndThrowError('nationalCardPicture', $errorMessage);
        }
        if ($this->Q_idBookletPicture === null or
            (is_a($this->Q_idBookletPicture, FileInstanceSame::class) and UserService::user()->idBookletPicture === null)) {
            $this->addAndThrowError('idBookletPicture', $errorMessage);
        }
        if ($this->Q_cellphoneNumber === null) {
            $this->addAndThrowError('cellphoneNumber', $errorMessage);
        }
        if ($this->Q_address === null) {
            $this->addAndThrowError('address', $errorMessage);
        }
        if ($this->Q_landlinePhoneNumber === null) {
            $this->addAndThrowError('landlinePhoneNumber', $errorMessage);
        }
        if ($this->Q_iban === null) {
            $this->addAndThrowError('iban', $errorMessage);
        }
    }

}