<?php

namespace Methods\Admin\Product;

use Entities\Farm;
use Entities\Product;
use Entities\Product\Category;
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
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetMany extends GetManyPagerAdmin
{
    protected static function entity(): string
    {
        return Product::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Product::F_id(),
            Product::F_title(),
            Product::F_price(),
            Product::F_rate(),
            (new EntityFieldSignatureTree(Product::F_category()))->addChild(Category::F_title()),
            (new EntityFieldSignatureTree(Product::F_farm()))->addChild(Farm::F_title()),
            Product::F_stock(),
        ]);
    }
}