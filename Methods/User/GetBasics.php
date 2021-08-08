<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
use Services\XUA\FileInstance;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\VARQUE\MethodView;

/**
 * @property ?FileInstance profilePicture
 * @method static MethodItemSignature R_profilePicture() The Signature of: Response Item `profilePicture`
 * @property ?string firstNameFa
 * @method static MethodItemSignature R_firstNameFa() The Signature of: Response Item `firstNameFa`
 * @property ?string lastNameFa
 * @method static MethodItemSignature R_lastNameFa() The Signature of: Response Item `lastNameFa`
 * @property ?string nationalCode
 * @method static MethodItemSignature R_nationalCode() The Signature of: Response Item `nationalCode`
 * @property ?string cellphoneNumber
 * @method static MethodItemSignature R_cellphoneNumber() The Signature of: Response Item `cellphoneNumber`
 * @property ?string email
 * @method static MethodItemSignature R_email() The Signature of: Response Item `email`
 */
class GetBasics extends MethodView
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            User::F_profilePicture(),
            User::F_firstNameFa(),
            User::F_lastNameFa(),
            User::F_nationalCode(),
            User::F_cellphoneNumber(),
            User::F_email(),
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