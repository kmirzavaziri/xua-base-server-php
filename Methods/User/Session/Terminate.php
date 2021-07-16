<?php

namespace Methods\User\Session;

use Entities\User\Session;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_sessionId
 * @method static MethodItemSignature Q_sessionId() The Signature of: Request Item `sessionId`
 */
class Terminate extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'sessionId' => new MethodItemSignature(Session::F_id()->type, true, null, false),
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
        ]);
    }

    protected function body(): void
    {
        $user = UserService::user();
        if (!$user->id) {
            $this->addAndThrowError('', ExpressionService::get('errormessage.access.denied'));
        }

        $session = new Session($this->Q_sessionId);
        if (!$session->id or $session->user->id != $user->id) {
            $this->addAndThrowError('sessionId', ExpressionService::get('errormessage.session.id.not.found'));
        }

        if ($session->id == UserService::session()->id) {
            $this->addAndThrowError('sessionId', ExpressionService::get('errormessage.cannot.remove.current.session'));
        }

        $session->delete();
    }
}