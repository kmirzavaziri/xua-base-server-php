<?php

namespace Methods\Admin\Product\Category;

use Entities\Product\Category;
use Services\UserService;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodQuery;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetAll extends MethodQuery
{
    protected static function entity(): string
    {
        return Category::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Category::F_id(),
            Category::F_title(),
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

    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}