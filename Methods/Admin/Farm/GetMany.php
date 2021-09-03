<?php

namespace Methods\Admin\Farm;

use Entities\Farm;
use Entities\User;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\EntityInstantField;
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
            (new EntityFieldSignatureTree(Farm::F_owner()))->addChild(User::F_titleFa()),
            new EntityInstantField('ostan', function (Farm $farm) { return $farm->ostan->title;}),
            new EntityInstantField('shahrestan', function (Farm $farm) { return $farm->shahrestan->title;}),
            new EntityInstantField('bakhsh', function (Farm $farm) { return $farm->bakhsh?->title;}),
            new EntityInstantField('dehestan', function (Farm $farm) { return $farm->dehestan?->title;}),
        ]);
    }
}