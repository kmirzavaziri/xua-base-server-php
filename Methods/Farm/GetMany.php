<?php

namespace Methods\Farm;

use Entities\Farm;
use Entities\User;
use Methods\Abstraction\GetManyPager;
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
class GetMany extends GetManyPager
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
            Farm::F_description(),
            Farm::F_story(),
            Farm::F_averageAnnualInterest(),
            Farm::F_rate(),
            Farm::F_image(),
            (new EntityFieldSignatureTree(Farm::F_agent()))->addChild(User::F_titleFa()),
        ]);
    }
}