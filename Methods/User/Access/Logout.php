<?php

namespace Methods\User\Access;

use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Method;

class Logout extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
        ]);
    }

    protected function execute(): void
    {
        $session = UserService::session();
        if (!$session->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.access.denied'));
        }
        $session->delete();
    }
}