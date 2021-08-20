<?php

namespace Methods\Admin\Product\Category;

use Entities\Product\Category;
use Entities\Product\FieldSignature;
use Methods\Abstraction\SetOneByIdAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property string Q_title
 * @method static MethodItemSignature Q_title() The Signature of: Request Item `title`
 * @property array Q_additionalFields
 * @method static MethodItemSignature Q_additionalFields() The Signature of: Request Item `additionalFields`
 */
class SetOne extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Category::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Category::F_title(),
            (new EntityFieldSignatureTree(Category::F_additionalFields()))->addChildren([
                FieldSignature::F_id(),
                FieldSignature::F_name(),
                FieldSignature::F_title(),
                FieldSignature::F_type(),
                FieldSignature::F_typeParams(),
            ]),
        ], false);
    }
}