<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use Services\XUA\Entity\EntityExtractService;
use Services\XUA\ExpressionService;
use Supers\Basics\Highers\StructuredMap;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array|object user
 * @method static MethodItemSignature R_user() The Signature of: Response Item `user`
 */
class GetMinimal extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            'user' => new MethodItemSignature(new StructuredMap(['structure' => []]), true, null, false),
        ]);
    }

    protected function execute(): void
    {
        $user = UserService::user();
        if (!$user->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.access.denied'));
        }
        $this->user = EntityExtractService::fields($user, [User::F_profilePicture(), User::F_firstNameFa(), User::F_lastNameFa()]);
    }
}