<?php

namespace Methods\User\Session;

use Entities\User;
use Entities\User\Session;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Method;
use XUA\Tools\Entity\Condition;

class TerminateAllOthers extends Method
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
        $user = UserService::user();
        if (!$user->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.access.denied'));
        }

        Session::deleteMany(
            Condition::leaf(Session::C_user()->rel(User::C_id()), Condition::EQ, $user->id)
                ->and(Session::C_id(), Condition::NEQ, UserService::session()->id)
        );
    }
}