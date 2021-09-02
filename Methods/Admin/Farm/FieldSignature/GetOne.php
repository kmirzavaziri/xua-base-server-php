<?php

namespace Methods\Admin\Farm\FieldSignature;

use Entities\Farm\FieldSignature;
use Methods\Abstraction\GetOneByIdAdmin;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property int id
 * @method static MethodItemSignature R_id() The Signature of: Response Item `id`
 * @property string title
 * @method static MethodItemSignature R_title() The Signature of: Response Item `title`
 * @property ?string type
 * @method static MethodItemSignature R_type() The Signature of: Response Item `type`
 * @property ?array typeParams
 * @method static MethodItemSignature R_typeParams() The Signature of: Response Item `typeParams`
 */
class GetOne extends GetOneByIdAdmin
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
        ]);
    }
}