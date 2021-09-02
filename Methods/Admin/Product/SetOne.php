<?php

namespace Methods\Admin\Product;

use Entities\Product;
use Entities\Product\Field;
use Methods\Abstraction\SetOneByIdAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property string Q_title
 * @method static MethodItemSignature Q_title() The Signature of: Request Item `title`
 * @property string Q_description
 * @method static MethodItemSignature Q_description() The Signature of: Request Item `description`
 * @property array Q_additionalFields
 * @method static MethodItemSignature Q_additionalFields() The Signature of: Request Item `additionalFields`
 * @property ?string Q_investmentTimespan
 * @method static MethodItemSignature Q_investmentTimespan() The Signature of: Request Item `investmentTimespan`
 * @property int Q_price
 * @method static MethodItemSignature Q_price() The Signature of: Request Item `price`
 * @property int Q_category
 * @method static MethodItemSignature Q_category() The Signature of: Request Item `category`
 * @property array Q_costsTable
 * @method static MethodItemSignature Q_costsTable() The Signature of: Request Item `costsTable`
 * @property array Q_predictionsTable
 * @method static MethodItemSignature Q_predictionsTable() The Signature of: Request Item `predictionsTable`
 * @property int Q_farm
 * @method static MethodItemSignature Q_farm() The Signature of: Request Item `farm`
 * @property ?array Q_paymentPlan
 * @method static MethodItemSignature Q_paymentPlan() The Signature of: Request Item `paymentPlan`
 */
class SetOne extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Product::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Product::F_title(),
            Product::F_description(),
            (new EntityFieldSignatureTree(Product::F_additionalFields()))->addChildren([
                Field::F_id(),
                Field::F_fieldSignature(),
                Field::F_value(),
            ]),
            Product::F_investmentTimespan(),
            Product::F_price(),
            Product::F_category(),
            Product::F_costsTable(),
            Product::F_predictionsTable(),
            Product::F_farm(),
            Product::F_paymentPlan(),
        ], false);
    }
}