<?php

namespace Methods\Farm;

use Entities\Farm;
use Entities\Farm\Field;
use Entities\Farm\FieldSignature;
use Entities\Farm\Media;
use Entities\Product;
use Entities\User;
use Methods\Abstraction\GetOneById;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\EntityInstantField;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property string title
 * @method static MethodItemSignature R_title() The Signature of: Response Item `title`
 * @property string description
 * @method static MethodItemSignature R_description() The Signature of: Response Item `description`
 * @property ?string story
 * @method static MethodItemSignature R_story() The Signature of: Response Item `story`
 * @property array additionalFields
 * @method static MethodItemSignature R_additionalFields() The Signature of: Response Item `additionalFields`
 * @property int|float averageAnnualInterest
 * @method static MethodItemSignature R_averageAnnualInterest() The Signature of: Response Item `averageAnnualInterest`
 * @property float rate
 * @method static MethodItemSignature R_rate() The Signature of: Response Item `rate`
 * @property array gallery
 * @method static MethodItemSignature R_gallery() The Signature of: Response Item `gallery`
 * @property array agent
 * @method static MethodItemSignature R_agent() The Signature of: Response Item `agent`
 * @property array products
 * @method static MethodItemSignature R_products() The Signature of: Response Item `products`
 * @property mixed ostan
 * @method static MethodItemSignature R_ostan() The Signature of: Response Item `ostan`
 * @property mixed shahrestan
 * @method static MethodItemSignature R_shahrestan() The Signature of: Response Item `shahrestan`
 * @property mixed bakhsh
 * @method static MethodItemSignature R_bakhsh() The Signature of: Response Item `bakhsh`
 * @property mixed dehestan
 * @method static MethodItemSignature R_dehestan() The Signature of: Response Item `dehestan`
 */
class GetOne extends GetOneById
{
    protected static function entity(): string
    {
        return Farm::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Farm::F_title(),
            Farm::F_description(),
            Farm::F_story(),
            (new EntityFieldSignatureTree(Farm::F_additionalFields()))->addChildren([
                (new EntityFieldSignatureTree(Field::F_fieldSignature()))->addChild(FieldSignature::F_title()),
                Field::F_value(),
            ]),
            Farm::F_averageAnnualInterest(),
            Farm::F_rate(),
            (new EntityFieldSignatureTree(Farm::F_gallery()))->addChildren([
                Media::F_id(),
                Media::F_source(),
            ]),
            (new EntityFieldSignatureTree(Farm::F_agent()))->addChildren([
                User::F_id(),
                User::F_titleFa(),
                User::F_profilePicture(),
            ]),
            (new EntityFieldSignatureTree(Farm::F_products()))->addChildren([
                Product::F_id(),
                Product::F_title(),
                Product::F_price(),
                Product::F_image(),
            ]),
            new EntityInstantField('ostan', function (Farm $farm) { return $farm->ostan->title;}),
            new EntityInstantField('shahrestan', function (Farm $farm) { return $farm->shahrestan->title;}),
            new EntityInstantField('bakhsh', function (Farm $farm) { return $farm->bakhsh?->title;}),
            new EntityInstantField('dehestan', function (Farm $farm) { return $farm->dehestan?->title;}),
        ]);
    }
}