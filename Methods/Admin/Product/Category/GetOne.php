<?php

namespace Methods\Admin\Product\Category;

use Entities\Product\Category;
use Entities\Product\FieldSignature;
use Methods\Abstraction\GetOneByIdAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property int id
 * @method static MethodItemSignature R_id() The Signature of: Response Item `id`
 * @property string title
 * @method static MethodItemSignature R_title() The Signature of: Response Item `title`
 * @property array additionalFields
 * @method static MethodItemSignature R_additionalFields() The Signature of: Response Item `additionalFields`
 */
class GetOne extends GetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Category::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Category::F_id(),
            Category::F_title(),
            (new EntityFieldSignatureTree(Category::F_additionalFields()))->addChildren([
                FieldSignature::F_id(),
                FieldSignature::F_title(),
                FieldSignature::F_type(),
                FieldSignature::F_typeParams(),
            ]),
        ]);
    }
}