<?php

namespace Methods\Admin\Farm;

use Entities\Farm;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_pageSize
 * @method static MethodItemSignature Q_pageSize() The Signature of: Request Item `pageSize`
 * @property int Q_pageIndex
 * @method static MethodItemSignature Q_pageIndex() The Signature of: Request Item `pageIndex`
 * @property array farms
 * @method static MethodItemSignature R_farms() The Signature of: Response Item `farms`
 */
class GetMany extends GetManyPagerAdmin
{
    protected static function entity(): string
    {
        return Farm::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Farm::F_id(),
            Farm::F_title(),
        ]);
    }

    protected static function wrapper(): string
    {
        return 'farms';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return null;
    }

}