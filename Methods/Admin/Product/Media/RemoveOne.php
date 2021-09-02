<?php

namespace Methods\Admin\Product\Media;

use Entities\Product\Media;
use Methods\Abstraction\RemoveOneAdmin;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 */
class RemoveOne extends RemoveOneAdmin
{
    protected static function entity(): string
    {
        return Media::class;
    }
}