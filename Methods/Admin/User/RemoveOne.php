<?php

namespace Methods\Admin\User;

use Entities\User;
use Methods\Abstraction\RemoveOneByIdAdmin;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 */
class RemoveOne extends RemoveOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }
}