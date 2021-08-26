<?php

namespace Methods\Admin\Product\FieldSignature;

use Entities\Product\Category;
use Entities\Product\FieldSignature;
use Methods\Abstraction\GetAllAdmin;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_category
 * @method static MethodItemSignature Q_category() The Signature of: Request Item `category`
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetAllByCategory extends GetAllAdmin
{
    protected static function entity(): string
    {
        return FieldSignature::class;
    }

    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'category' => new MethodItemSignature(Category::F_id()->type, true, null, false),
        ]);
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            FieldSignature::F_id(),
            FieldSignature::F_title(),
            FieldSignature::F_type(),
            FieldSignature::F_typeParams(),
        ]);
    }

    protected function feed(): array
    {
        return FieldSignature::getMany(Condition::leaf(FieldSignature::C_category()->rel(Category::C_id()), Condition::EQ, $this->Q_category));
    }

    protected static function wrapper(): string
    {
        return 'result';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return null;
    }
}