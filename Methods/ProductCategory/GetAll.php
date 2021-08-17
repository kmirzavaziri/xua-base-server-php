<?php

namespace Methods\ProductCategory;

use Entities\ProductCategory;
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
        return ProductCategory::class;
    }

    protected static function fields(): array
    {
        return [
            ProductCategory::F_id(),
            ProductCategory::F_title(),
        ];
    }

    protected function feed(): array
    {
        return ProductCategory::getMany();
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