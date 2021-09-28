<?php

namespace Entities\Product;

use Entities\ChangeTracker;
use Entities\Product;
use Services\Mime;
use Services\Size;
use Services\XUA\FileInstance;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Files\Generic;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Entity\Index;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Services\XUA\DateTimeInstance createdAt
 * @method static EntityFieldSignature F_createdAt() The Signature of: Field `createdAt`
 * @method static ConditionField C_createdAt() The Condition Field of: Field `createdAt`
 * @property \Entities\User createdBy
 * @method static EntityFieldSignature F_createdBy() The Signature of: Field `createdBy`
 * @method static ConditionField C_createdBy() The Condition Field of: Field `createdBy`
 * @property \Services\XUA\DateTimeInstance updatedAt
 * @method static EntityFieldSignature F_updatedAt() The Signature of: Field `updatedAt`
 * @method static ConditionField C_updatedAt() The Condition Field of: Field `updatedAt`
 * @property \Entities\User updatedBy
 * @method static EntityFieldSignature F_updatedBy() The Signature of: Field `updatedBy`
 * @method static ConditionField C_updatedBy() The Condition Field of: Field `updatedBy`
 * @property \Entities\Product product
 * @method static EntityFieldSignature F_product() The Signature of: Field `product`
 * @method static ConditionField C_product() The Condition Field of: Field `product`
 * @property \Services\XUA\FileInstance source
 * @method static EntityFieldSignature F_source() The Signature of: Field `source`
 * @method static ConditionField C_source() The Condition Field of: Field `source`
 */
class Media extends ChangeTracker
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'product' => new EntityFieldSignature(
                static::class, 'product',
                new EntityRelation([
                    'relatedEntity' => \Entities\Product::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'gallery',
                    'definedOn' => EntityRelation::DEFINED_ON_THERE,
                ]),
                null
            ),
            'source' => new EntityFieldSignature(
                static::class, 'source',
                new Generic(['nullable' => false, 'maxSize' => 10 * Size::MB, 'allowedMimeTypes' => [Mime::MIME_IMAGE_JPEG, Mime::MIME_VIDEO_MP4]]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
        ]);
    }
}