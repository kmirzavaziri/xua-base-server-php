<?php

namespace Methods\Admin\Farm\FieldSignature;

use Entities\Farm\FieldSignature;
use Methods\Abstraction\GetAllAdmin;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_category
 * @method static MethodItemSignature Q_category() The Signature of: Request Item `category`
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetAll extends GetAllAdmin
{
    protected static function entity(): string
    {
        return FieldSignature::class;
    }

    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
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
}