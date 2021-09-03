<?php

namespace Methods\Farm\Rate;


use Entities\Farm;
use Entities\Farm\Rate;
use Entities\User;
use Services\UserService;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_farm
 * @method static MethodItemSignature Q_farm() The Signature of: Request Item `farm`
 * @property int rate
 * @method static MethodItemSignature R_rate() The Signature of: Response Item `rate`
 */
class RemoveMine extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'farm' => new MethodItemSignature(Farm::F_id()->type, true, null, false),
        ]);
    }

    protected function validations(): void
    {
        UserService::verifyUser($this->error);
    }

    protected function body(): void
    {
        Rate::getOne(
            Condition::leaf(Rate::C_farm()->rel(Farm::C_id()), Condition::EQ, $this->Q_farm)
                ->and(Rate::C_rater()->rel(User::C_id()), Condition::EQ, UserService::user()->id)
        )->delete();
    }
}