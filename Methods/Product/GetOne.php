<?php

namespace Methods\Product;

use Entities\Dataset\IranAdministrativeDivision;
use Entities\Farm;
use Entities\Product;
use Entities\Product\Field;
use Entities\Product\FieldSignature;
use Entities\Product\Media;
use Methods\Abstraction\GetOneById;
use Methods\Abstraction\GetOneByIdAdmin;
use Supers\Basics\EntitySupers\PhpVirtualField;
use Supers\Basics\Strings\Text;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\EntityInstantField;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property string title
 * @method static MethodItemSignature R_title() The Signature of: Response Item `title`
 * @property array gallery
 * @method static MethodItemSignature R_gallery() The Signature of: Response Item `gallery`
 * @property int category
 * @method static MethodItemSignature R_category() The Signature of: Response Item `category`
 * @property float rate
 * @method static MethodItemSignature R_rate() The Signature of: Response Item `rate`
 * @property mixed ostan
 * @method static MethodItemSignature R_ostan() The Signature of: Response Item `ostan`
 * @property mixed shahrestan
 * @method static MethodItemSignature R_shahrestan() The Signature of: Response Item `shahrestan`
 * @property mixed bakhsh
 * @method static MethodItemSignature R_bakhsh() The Signature of: Response Item `bakhsh`
 * @property mixed dehestan
 * @method static MethodItemSignature R_dehestan() The Signature of: Response Item `dehestan`
 * @property mixed farm
 * @method static MethodItemSignature R_farm() The Signature of: Response Item `farm`
 * @property string description
 * @method static MethodItemSignature R_description() The Signature of: Response Item `description`
 * @property ?string investmentTimespan
 * @method static MethodItemSignature R_investmentTimespan() The Signature of: Response Item `investmentTimespan`
 * @property int price
 * @method static MethodItemSignature R_price() The Signature of: Response Item `price`
 * @property array additionalFields
 * @method static MethodItemSignature R_additionalFields() The Signature of: Response Item `additionalFields`
 * @property array costsTable
 * @method static MethodItemSignature R_costsTable() The Signature of: Response Item `costsTable`
 * @property array predictionsTable
 * @method static MethodItemSignature R_predictionsTable() The Signature of: Response Item `predictionsTable`
 * @property ?\Services\XUA\FileInstance brochure
 * @method static MethodItemSignature R_brochure() The Signature of: Response Item `brochure`
 */
class GetOne extends GetOneById
{
    protected static function entity(): string
    {
        return Product::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Product::F_title(),
            (new EntityFieldSignatureTree(Product::F_gallery()))->addChildren([
                Media::F_id(),
                Media::F_source(),
            ]),
            Product::F_category(),
            Product::F_rate(),
            new EntityInstantField('ostan', function (Product $product) { return $product->farm->ostan->title;}),
            new EntityInstantField('shahrestan', function (Product $product) { return $product->farm->shahrestan->title;}),
            new EntityInstantField('bakhsh', function (Product $product) { return $product->farm->bakhsh?->title;}),
            new EntityInstantField('dehestan', function (Product $product) { return $product->farm->dehestan?->title;}),
            new EntityInstantField('farm', function (Product $product) { return $product->farm->title;}),
            Product::F_description(),
            Product::F_investmentTimespan(),
            Product::F_price(),
            (new EntityFieldSignatureTree(Product::F_additionalFields()))->addChildren([
                (new EntityFieldSignatureTree(Field::F_fieldSignature()))->addChild(FieldSignature::F_title()),
                Field::F_value(),
            ]),
            Product::F_costsTable(),
            Product::F_predictionsTable(),
            Product::F_brochure(),
        ]);
    }
}