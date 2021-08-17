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
 * @property ?string Q_organizationNameFa
 * @method static MethodItemSignature Q_organizationNameFa() The Signature of: Request Item `organizationNameFa`
 * @property ?string Q_organizationNameEn
 * @method static MethodItemSignature Q_organizationNameEn() The Signature of: Request Item `organizationNameEn`
 * @property ?string Q_organizationNationalId
 * @method static MethodItemSignature Q_organizationNationalId() The Signature of: Request Item `organizationNationalId`
 * @property ?string Q_organizationRegistrationId
 * @method static MethodItemSignature Q_organizationRegistrationId() The Signature of: Request Item `organizationRegistrationId`
 * @property ?string Q_faxNumber
 * @method static MethodItemSignature Q_faxNumber() The Signature of: Request Item `faxNumber`
 */
class SetJuridical extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            new VarqueMethodFieldSignature(User::F_organizationNameFa(), false, null, false),
            new VarqueMethodFieldSignature(User::F_organizationNameEn(), false, null, false),
            new VarqueMethodFieldSignature(User::F_organizationNationalId(), false, null, false),
            new VarqueMethodFieldSignature(User::F_organizationRegistrationId(), false, null, false),
            new VarqueMethodFieldSignature(User::F_faxNumber(), false, null, false),
        ];
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }

    protected function validations(): void
    {
        $errorMessage = ExpressionService::get('errormessage.required.request.item.not.provided');
        if ($this->Q_organizationNameFa === null) {
            $this->addAndThrowError('organizationNameFa', $errorMessage);
        }
        if ($this->Q_organizationNameEn === null) {
            $this->addAndThrowError('organizationNameEn', $errorMessage);
        }
        if ($this->Q_organizationNationalId === null) {
            $this->addAndThrowError('organizationNationalId', $errorMessage);
        }
        if ($this->Q_organizationRegistrationId === null) {
            $this->addAndThrowError('organizationRegistrationId', $errorMessage);
        }
        if ($this->Q_faxNumber === null) {
            $this->addAndThrowError('faxNumber', $errorMessage);
        }
    }
}