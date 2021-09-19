<?php

namespace Methods\Farm\FieldSignature;

use Entities\Farm\FieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\VARQUE\MethodQuery;

/**
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetAll extends MethodQuery
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