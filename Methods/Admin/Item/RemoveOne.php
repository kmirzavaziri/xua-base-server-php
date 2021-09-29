<?php

namespace Methods\Admin\Item;

use Entities\Item;
use Methods\Abstraction\RemoveOneByIdAdmin;

class RemoveOne extends RemoveOneByIdAdmin
{
    protected static function entity(): string
    {
        return Item::class;
    }
}