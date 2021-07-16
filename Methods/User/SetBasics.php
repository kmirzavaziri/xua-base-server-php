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
 * @property mixed Q_profilePicture
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
            new VarqueMethodFieldSignature(User::F_profilePicture(), false),
            new VarqueMethodFieldSignature(User::F_firstNameFa(), false),
            new VarqueMethodFieldSignature(User::F_lastNameFa(), false),
            new VarqueMethodFieldSignature(User::F_nationalCode(), false),
            new VarqueMethodFieldSignature(User::F_cellphoneNumber(), false),
            new VarqueMethodFieldSignature(User::F_email(), false),
        ];
    }

    protected function feed(): Entity
    {
        $user = UserService::user();
        if (!$user->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.access.denied'));
        }
        return $user;
    }
}