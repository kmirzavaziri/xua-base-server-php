<?php

namespace Methods\Product\Rate;


use Entities\Product;
use Entities\Product\Rate;
use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Method;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_product
 * @method static MethodItemSignature Q_product() The Signature of: Request Item `product`
 * @property int Q_rate
 * @method static MethodItemSignature Q_rate() The Signature of: Request Item `rate`
 */
class SetMine extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'product' => new MethodItemSignature(Product::F_id()->type, true, null, false),
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
            Condition::leaf(Rate::C_product()->rel(Product::C_id()), Condition::EQ, $this->Q_product)
                ->and(Rate::C_rater()->rel(User::C_id()), Condition::EQ, UserService::user()->id)
        );
        if (!$rate->id) {
            $product = new Product($this->Q_product);
            if (!$product->id) {
                $this->addAndThrowError('product', ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                    'entity' => ExpressionService::get('entityclass.' . Product::table()),
                    'id' => $this->Q_product
                ]));
            }
            $rate->product = $product;
            $rate->rater = UserService::user();
        }

        $rate->rate = $this->Q_rate;
        $rate->store();
    }
}