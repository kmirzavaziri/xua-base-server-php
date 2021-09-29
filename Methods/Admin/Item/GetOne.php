<?php

namespace Methods\Admin\Item;

use Entities\Item;
use Methods\Abstraction\GetOneByIdAdmin;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class GetOne extends GetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Item::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Item::F_product(),
            Item::F_code(),
            Item::F_status(),
            Item::F_order(),
        ]);
    }
}