<?php

namespace Methods\Product;

use Entities\Product;
use Methods\Abstraction\GetManyPager;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_pageSize
 * @method static MethodItemSignature Q_pageSize() The Signature of: Request Item `pageSize`
 * @property int Q_pageIndex
 * @method static MethodItemSignature Q_pageIndex() The Signature of: Request Item `pageIndex`
 * @property array products
 * @method static MethodItemSignature R_products() The Signature of: Response Item `products`
 */
class GetMany extends GetManyPager
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
            Product::F_image(),
        ]);
    }
}