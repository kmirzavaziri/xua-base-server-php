<?php

namespace Methods\User;

use Entities\User\Session;
use Services\XUA\DateTimeInstance;
use Supers\Basics\Numerics\Integer;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int count
 * @method static MethodItemSignature R_count() The Signature of: Response Item `count`
 */
class RemoveExpiredSessions extends Method
{
    public static function isPublic(): bool
    {
        return false;
    }

    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            'count' => new MethodItemSignature(new Integer([]), true, null, false),
        ]);
    }

    protected function execute(): void
    {
        $this->count = Session::deleteMany(
            Condition::leaf(Session::C_accessToken(), Condition::ISNULL)
                ->or(Session::C_accessToken(), Condition::EQ, '')
                ->and(Session::C_codeSentAt(), Condition::LESSEQ, (new DateTimeInstance())->dist(new DateTimeInstance(2 * DateTimeInstance::MINUTE)))
        );
    }
}