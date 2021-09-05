<?php

namespace Methods\Admin\Farm;


use Entities\Farm;
use Entities\Farm\Field;
use Entities\Farm\Media;
use Entities\Product;
use Entities\User;
use Methods\Abstraction\GetOneByIdAdmin;
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
 * @property string status
 * @method static MethodItemSignature R_status() The Signature of: Response Item `status`
 * @property array gallery
 * @method static MethodItemSignature R_gallery() The Signature of: Response Item `gallery`
 * @property array agent
 * @method static MethodItemSignature R_agent() The Signature of: Response Item `agent`
 * @property string agentType
 * @method static MethodItemSignature R_agentType() The Signature of: Response Item `agentType`
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
 * @property ?string address
 * @method static MethodItemSignature R_address() The Signature of: Response Item `address`
 * @property null|int|float geolocationLat
 * @method static MethodItemSignature R_geolocationLat() The Signature of: Response Item `geolocationLat`
 * @property null|int|float geolocationLong
 * @method static MethodItemSignature R_geolocationLong() The Signature of: Response Item `geolocationLong`
 * @property ?string cooperationField
 * @method static MethodItemSignature R_cooperationField() The Signature of: Response Item `cooperationField`
 * @property ?\Services\XUA\FileInstance proposal
 * @method static MethodItemSignature R_proposal() The Signature of: Response Item `proposal`
 */
class GetOne extends GetOneByIdAdmin
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
                Field::F_id(),
                Field::F_fieldSignature(),
                Field::F_value(),
            ]),
            Farm::F_averageAnnualInterest(),
            Farm::F_rate(),
            Farm::F_status(),
            (new EntityFieldSignatureTree(Farm::F_gallery()))->addChildren([
                Media::F_id(),
                Media::F_source(),
            ]),
            (new EntityFieldSignatureTree(Farm::F_agent()))->addChildren([
                User::F_id(),
                User::F_titleFa(),
            ]),
            Farm::F_agentType(),
            (new EntityFieldSignatureTree(Farm::F_products()))->addChildren([
                Product::F_id(),
                Product::F_title(),
            ]),
            new EntityInstantField('ostan', function (Farm $farm) { return $farm->ostan->id;}),
            new EntityInstantField('shahrestan', function (Farm $farm) { return $farm->shahrestan->id;}),
            new EntityInstantField('bakhsh', function (Farm $farm) { return $farm->bakhsh?->id;}),
            new EntityInstantField('dehestan', function (Farm $farm) { return $farm->dehestan?->id;}),
            Farm::F_address(),
            Farm::F_geolocationLat(),
            Farm::F_geolocationLong(),
            Farm::F_cooperationField(),
            Farm::F_proposal(),
        ]);
    }
}