<?php

namespace Methods\Product\Rate;

use Entities\Product;
use Entities\Product\Rate;
use Entities\User;
use Services\UserService;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_product
 * @method static MethodItemSignature Q_product() The Signature of: Request Item `product`
 * @property null|int rate
 * @method static MethodItemSignature R_rate() The Signature of: Response Item `rate`
 */
class GetMine extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'product' => new MethodItemSignature(Product::F_id()->type, true, null, false),
        ]);
    }

    protected static function _responseSignatures(): array
    {
        $rateType = Rate::F_rate()->type;
        $rateType->nullable = true;
        return array_merge(parent::_requestSignatures(), [
            'rate' => new MethodItemSignature($rateType, true, null, false),
        ]);
    }

    protected function validations(): void
    {
        UserService::verifyUser($this->error);
    }

    protected function body(): void
    {
        $rate = Rate::getOne(
            Condition::leaf(Rate::C_product()->rel(Product::C_id()), Condition::EQ, $this->Q_product)
                ->and(Rate::C_rater()->rel(User::C_id()), Condition::EQ, UserService::user()->id)
        );
        if ($rate->id) {
            $this->rate = $rate->rate;
        } else {
            $this->rate = null;
        }
    }
}