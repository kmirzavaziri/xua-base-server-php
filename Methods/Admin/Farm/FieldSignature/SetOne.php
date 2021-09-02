<?php

namespace Methods\Admin\Farm\FieldSignature;

use Entities\Farm\FieldSignature;
use Methods\Abstraction\SetOneByIdAdmin;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property string Q_title
 * @method static MethodItemSignature Q_title() The Signature of: Request Item `title`
 * @property ?string Q_type
 * @method static MethodItemSignature Q_type() The Signature of: Request Item `type`
 * @property ?array Q_typeParams
 * @method static MethodItemSignature Q_typeParams() The Signature of: Request Item `typeParams`
 */
class SetOne extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return FieldSignature::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            FieldSignature::F_id(),
            FieldSignature::F_title(),
            FieldSignature::F_type(),
            FieldSignature::F_typeParams(),
        ], false);
    }
}