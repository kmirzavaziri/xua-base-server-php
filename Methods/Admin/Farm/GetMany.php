<?php

namespace Methods\Admin\Farm;

use Entities\Farm;
use Entities\User;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
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
        return Farm::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Farm::F_id(),
            Farm::F_title(),
            Farm::F_rate(),
            Farm::F_status(),
            Farm::F_averageAnnualInterest(),
            (new EntityFieldSignatureTree(Farm::F_agent()))->addChild(User::F_titleFa()),
        ]);
    }
}