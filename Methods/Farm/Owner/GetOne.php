<?php

namespace Methods\Farm\Owner;

use Entities\Farm;
use Entities\Farm\Field;
use Entities\Farm\FieldSignature;
use Entities\Farm\Media;
use Entities\Product;
use Entities\User;
use Methods\Abstraction\GetOneById;
use Services\XUA\ExpressionService;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\EntityInstantField;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property string titleFa
 * @method static MethodItemSignature R_titleFa() The Signature of: Response Item `titleFa`
 * @property ?\Services\XUA\FileInstance profilePicture
 * @method static MethodItemSignature R_profilePicture() The Signature of: Response Item `profilePicture`
 * @property array farms
 * @method static MethodItemSignature R_farms() The Signature of: Response Item `farms`
 */
class GetOne extends GetOneById
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            User::F_titleFa(),
            User::F_profilePicture(),
            (new EntityFieldSignatureTree(User::F_farms()))->addChildren([
                Farm::F_id(),
                Farm::F_image(),
                Farm::F_title(),
                Farm::F_averageAnnualInterest(),
                Farm::F_rate(),
            ]),
        ]);
    }

    protected function validations(): void
    {
        parent::validations();
        if (!$this->feed()->farms) {
            $this->addAndThrowError('id', ExpressionService::get('errormessage.invalid.id'));
        }
    }
}