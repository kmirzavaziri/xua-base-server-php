<?php

namespace Methods\Product\Category;

use Entities\Product\Category;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\VARQUE\MethodQuery;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array productCategories
 * @method static MethodItemSignature R_productCategories() The Signature of: Response Item `productCategories`
 */
class GetAll extends MethodQuery
{
    protected static function entity(): string
    {
        return Category::class;
    }

    protected static function fields(): array
    {
        return [
            Category::F_id(),
            Category::F_title(),
        ];
    }

    protected static function wrapper(): string
    {
        return 'productCategories';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return null;
    }
}