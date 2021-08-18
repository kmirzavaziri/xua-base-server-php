<?php

namespace Methods\Abstraction;

use Services\UserService;
use Supers\Basics\Numerics\DecimalRange;
use XUA\Tools\Entity\Pager;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\VARQUE\MethodQuery;

/**
 * @property int Q_pageSize
 * @method static MethodItemSignature Q_pageSize() The Signature of: Request Item `pageSize`
 * @property int Q_pageIndex
 * @method static MethodItemSignature Q_pageIndex() The Signature of: Request Item `pageIndex`
 */
abstract class GetManyPagerAdmin extends MethodQuery
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'pageSize' => new MethodItemSignature(new DecimalRange(['min' => 1, 'max' => 1_000, 'fractionalLength' => 0]), false, 20, false),
            'pageIndex' => new MethodItemSignature(new DecimalRange(['min' => 1, 'fractionalLength' => 0]), false, 1, false),
        ]);
    }

    protected function pager(): Pager
    {
        return new Pager($this->Q_pageSize, $this->Q_pageIndex);
    }

    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}