<?php

namespace Methods\Farm\Rate;


use Entities\Farm;
use Entities\Farm\Rate;
use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Entity;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodView;

/**
 * @property int Q_farm
 * @method static MethodItemSignature Q_farm() The Signature of: Request Item `farm`
 * @property int Q_rate
 * @method static MethodItemSignature Q_rate() The Signature of: Request Item `rate`
 */
class SetMine extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'farm' => new MethodItemSignature(Farm::F_id()->type, true, null, false),
            'rate' => new MethodItemSignature(Rate::F_rate()->type, true, null, false),
        ]);
    }

    protected function validations(): void
    {
        UserService::verifyUser($this->error);
    }

    protected function body(): void
    {
        $rate = Rate::getOne(
            Condition::leaf(Rate::C_farm()->rel(Farm::C_id()), Condition::EQ, $this->Q_farm)
                ->and(Rate::C_rater()->rel(User::C_id()), Condition::EQ, UserService::user()->id)
        );
        if (!$rate->id) {
            $farm = new Farm($this->Q_farm);
            if (!$farm->id) {
                $this->addAndThrowError('farm', ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                    'entity' => ExpressionService::get('entityclass.' . Farm::table()),
                    'id' => $this->Q_farm
                ]));
            }
            $rate->farm = $farm;
            $rate->rater = UserService::user();
        }

        $rate->rate = $this->Q_rate;
        $rate->store();
    }
}