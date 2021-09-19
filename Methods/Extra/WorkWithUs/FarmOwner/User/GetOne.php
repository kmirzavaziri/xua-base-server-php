<?php

namespace Methods\Extra\WorkWithUs\FarmOwner\User;

use Entities\User;
use Entities\User\Info\FarmOwner;
use Services\UserService;
use XUA\Entity;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodView;

/**
 * @property ?string nationality
 * @method static MethodItemSignature R_nationality() The Signature of: Response Item `nationality`
 * @property null|\Services\XUA\DateTimeInstance birthDate
 * @method static MethodItemSignature R_birthDate() The Signature of: Response Item `birthDate`
 * @property ?string gender
 * @method static MethodItemSignature R_gender() The Signature of: Response Item `gender`
 * @property ?string nationalCode
 * @method static MethodItemSignature R_nationalCode() The Signature of: Response Item `nationalCode`
 * @property ?string firstNameFa
 * @method static MethodItemSignature R_firstNameFa() The Signature of: Response Item `firstNameFa`
 * @property ?string lastNameFa
 * @method static MethodItemSignature R_lastNameFa() The Signature of: Response Item `lastNameFa`
 * @property ?string faxNumber
 * @method static MethodItemSignature R_faxNumber() The Signature of: Response Item `faxNumber`
 * @property ?string website
 * @method static MethodItemSignature R_website() The Signature of: Response Item `website`
 * @property ?string email
 * @method static MethodItemSignature R_email() The Signature of: Response Item `email`
 * @property ?string iban
 * @method static MethodItemSignature R_iban() The Signature of: Response Item `iban`
 * @property ?string bankAccountNo
 * @method static MethodItemSignature R_bankAccountNo() The Signature of: Response Item `bankAccountNo`
 * @property ?string bankTitle
 * @method static MethodItemSignature R_bankTitle() The Signature of: Response Item `bankTitle`
 * @property ?string address
 * @method static MethodItemSignature R_address() The Signature of: Response Item `address`
 * @property ?string postalCode
 * @method static MethodItemSignature R_postalCode() The Signature of: Response Item `postalCode`
 * @property ?\Services\XUA\FileInstance profilePicture
 * @method static MethodItemSignature R_profilePicture() The Signature of: Response Item `profilePicture`
 * @property ?string firstNameEn
 * @method static MethodItemSignature R_firstNameEn() The Signature of: Response Item `firstNameEn`
 * @property ?string lastNameEn
 * @method static MethodItemSignature R_lastNameEn() The Signature of: Response Item `lastNameEn`
 * @property ?string cellphoneNumber
 * @method static MethodItemSignature R_cellphoneNumber() The Signature of: Response Item `cellphoneNumber`
 * @property ?string landlinePhoneNumber
 * @method static MethodItemSignature R_landlinePhoneNumber() The Signature of: Response Item `landlinePhoneNumber`
 * @property ?string referralMethod
 * @method static MethodItemSignature R_referralMethod() The Signature of: Response Item `referralMethod`
 * @property ?string referralDetails
 * @method static MethodItemSignature R_referralDetails() The Signature of: Response Item `referralDetails`
 * @property ?\Services\XUA\FileInstance nationalCardPicture
 * @method static MethodItemSignature R_nationalCardPicture() The Signature of: Response Item `nationalCardPicture`
 * @property ?\Services\XUA\FileInstance idBookletPicture
 * @method static MethodItemSignature R_idBookletPicture() The Signature of: Response Item `idBookletPicture`
 * @property ?string bio
 * @method static MethodItemSignature R_bio() The Signature of: Response Item `bio`
 * @property ?array infoFarmOwner
 * @method static MethodItemSignature R_infoFarmOwner() The Signature of: Response Item `infoFarmOwner`
 */
class GetOne extends MethodView
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
            User::F_bankAccountNo(),
            User::F_bankTitle(),
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
}