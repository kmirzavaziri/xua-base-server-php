<?php

namespace Methods\Admin\Product\Category;

use Entities\Product\Category;
use Methods\Abstraction\GetAllAdmin;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetAll extends GetAllAdmin
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
}