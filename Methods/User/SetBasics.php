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
 * @property ?\Services\XUA\FileInstance Q_profilePicture
 * @method static MethodItemSignature Q_profilePicture() The Signature of: Request Item `profilePicture`
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
 * @property ?string Q_bio
 * @method static MethodItemSignature Q_bio() The Signature of: Request Item `bio`
 */
class SetBasics extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_profilePicture(),
            User::F_firstNameFa(),
            User::F_lastNameFa(),
            User::F_nationalCode(),
            User::F_cellphoneNumber(),
            User::F_email(),
            User::F_bio(),
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