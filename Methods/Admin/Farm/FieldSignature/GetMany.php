<?php

namespace Methods\Admin\Farm\FieldSignature;

use Entities\Farm;
use Entities\Farm\FieldSignature;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_pageSize
 * @method static MethodItemSignature Q_pageSize() The Signature of: Request Item `pageSize`
 * @property int Q_pageIndex
 * @method static MethodItemSignature Q_pageIndex() The Signature of: Request Item `pageIndex`
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetMany extends GetManyPagerAdmin
{
    protected static function entity(): string
    {
        return FieldSignature::class;
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

    protected static function wrapper(): string
    {
        return 'result';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return null;
    }

}