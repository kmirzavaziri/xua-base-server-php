<?php

namespace Methods\Admin\Product;

use Entities\Dataset\IranAdministrativeDivision;
use Entities\Product;
use Entities\Product\Field;
use Entities\Product\Media;
use Methods\Abstraction\GetOneByIdAdmin;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property string title
 * @method static MethodItemSignature R_title() The Signature of: Response Item `title`
 * @property string description
 * @method static MethodItemSignature R_description() The Signature of: Response Item `description`
 * @property array additionalFields
 * @method static MethodItemSignature R_additionalFields() The Signature of: Response Item `additionalFields`
 * @property ?string investmentTimespan
 * @method static MethodItemSignature R_investmentTimespan() The Signature of: Response Item `investmentTimespan`
 * @property ?\Services\XUA\FileInstance brochure
 * @method static MethodItemSignature R_brochure() The Signature of: Response Item `brochure`
 * @property int price
 * @method static MethodItemSignature R_price() The Signature of: Response Item `price`
 * @property array gallery
 * @method static MethodItemSignature R_gallery() The Signature of: Response Item `gallery`
 * @property int category
 * @method static MethodItemSignature R_category() The Signature of: Response Item `category`
 * @property array costsTable
 * @method static MethodItemSignature R_costsTable() The Signature of: Response Item `costsTable`
 * @property array predictionsTable
 * @method static MethodItemSignature R_predictionsTable() The Signature of: Response Item `predictionsTable`
 * @property \int ostan
 * @method static MethodItemSignature R_ostan() The Signature of: Response Item `ostan`
 * @property \int shahrestan
 * @method static MethodItemSignature R_shahrestan() The Signature of: Response Item `shahrestan`
 * @property \?int bakhsh
 * @method static MethodItemSignature R_bakhsh() The Signature of: Response Item `bakhsh`
 * @property \?int dehestan
 * @method static MethodItemSignature R_dehestan() The Signature of: Response Item `dehestan`
 * @property int farm
 * @method static MethodItemSignature R_farm() The Signature of: Response Item `farm`
 * @property ?array paymentPlan
 * @method static MethodItemSignature R_paymentPlan() The Signature of: Response Item `paymentPlan`
 */
class GetOne extends GetOneByIdAdmin
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
            Product::F_brochure(),
            Product::F_price(),
            (new EntityFieldSignatureTree(Product::F_gallery()))->addChildren([
                Media::F_id(),
                Media::F_source(),
            ]),
            Product::F_category(),
            Product::F_costsTable(),
            Product::F_predictionsTable(),
            Product::F_ostan(),
            Product::F_shahrestan(),
            Product::F_bakhsh(),
            Product::F_dehestan(),
            Product::F_farm(),
            Product::F_paymentPlan(),
        ]);
    }
}