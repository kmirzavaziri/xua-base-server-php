<?php

namespace Methods\Admin\Product\Category;

use Entities\Product\Category;
use Entities\Product\FieldSignature;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_pageSize
 * @method static MethodItemSignature Q_pageSize() The Signature of: Request Item `pageSize`
 * @property int Q_pageIndex
 * @method static MethodItemSignature Q_pageIndex() The Signature of: Request Item `pageIndex`
 * @property array productCategories
 * @method static MethodItemSignature R_productCategories() The Signature of: Response Item `productCategories`
 */
class GetMany extends GetManyPagerAdmin
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
            (new EntityFieldSignatureTree(Category::F_additionalFields()))->addChildren([
                FieldSignature::F_title(),
            ]),
        ]);
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