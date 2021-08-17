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
 * @property ?string Q_firstNameFa
 * @method static MethodItemSignature Q_firstNameFa() The Signature of: Request Item `firstNameFa`
 * @property ?string Q_lastNameFa
 * @method static MethodItemSignature Q_lastNameFa() The Signature of: Request Item `lastNameFa`
 * @property ?string Q_nationalCode
 * @method static MethodItemSignature Q_nationalCode() The Signature of: Request Item `nationalCode`
 * @property ?string Q_cellphoneNumber
 * @method static MethodItemSignature Q_cellphoneNumber() The Signature of: Request Item `cellphoneNumber`
 * @property ?string Q_email
 * @method static MethodItemSignature Q_email() The Signature of: Request Item `email`
 */
class SetBasics extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            new VarqueMethodFieldSignature(User::F_firstNameFa(), false, null, false),
            new VarqueMethodFieldSignature(User::F_lastNameFa(), false, null, false),
            new VarqueMethodFieldSignature(User::F_nationalCode(), false, null, false),
            new VarqueMethodFieldSignature(User::F_cellphoneNumber(), false, null, false),
            new VarqueMethodFieldSignature(User::F_email(), false, null, false),
        ];
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
        if ($this->Q_nationalCode === null) {
            $this->addAndThrowError('nationalCode', $errorMessage);
        }
        if ($this->Q_cellphoneNumber === null) {
            $this->addAndThrowError('cellphoneNumber', $errorMessage);
        }
        if ($this->Q_email === null) {
            $this->addAndThrowError('email', $errorMessage);
        }
    }
}