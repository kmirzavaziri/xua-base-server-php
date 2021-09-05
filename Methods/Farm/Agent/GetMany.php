<?php

namespace Methods\Farm\Agent;

use Entities\Farm;
use Entities\User;
use Methods\Abstraction\GetManyPager;
use XUA\Tools\Entity\Condition;
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
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_id(),
            User::F_titleFa(),
            User::F_profilePicture(),
        ]);
    }

    protected function condition(): Condition
    {
        return Condition::leaf(User::C_farms()->rel(Farm::C_id()), Condition::NISNULL);
    }
}