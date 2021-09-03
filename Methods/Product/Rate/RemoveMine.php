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
 */
class RemoveMine extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'product' => new MethodItemSignature(Product::F_id()->type, true, null, false),
        ]);
    }

    protected function validations(): void
    {
        UserService::verifyUser($this->error);
    }

    protected function body(): void
    {
        Rate::getOne(
            Condition::leaf(Rate::C_product()->rel(Product::C_id()), Condition::EQ, $this->Q_product)
                ->and(Rate::C_rater()->rel(User::C_id()), Condition::EQ, UserService::user()->id)
        )->delete();
    }
}