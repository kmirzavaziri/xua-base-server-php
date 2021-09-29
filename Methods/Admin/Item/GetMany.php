<?php

namespace Methods\Admin\Item;

use Entities\Item;
use Entities\Product;
use Methods\Abstraction\GetManyPagerAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class GetMany extends GetManyPagerAdmin
{
    protected static function entity(): string
    {
        return Item::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Item::F_id(),
            (new EntityFieldSignatureTree(Item::F_product()))->addChild(Product::F_title()),
            Item::F_code(),
            Item::F_status(),
        ]);
    }
}