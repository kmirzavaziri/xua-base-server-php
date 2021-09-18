<?php

namespace Entities\Farm;

use Entities\Farm;
use Services\Mime;
use Services\Size;
use Services\XUA\FileInstance;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Files\Generic;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property Farm farm
 * @method static EntityFieldSignature F_farm() The Signature of: Field `farm`
 * @method static ConditionField C_farm() The Condition Field of: Field `farm`
 * @property FileInstance source
 * @method static EntityFieldSignature F_source() The Signature of: Field `source`
 * @method static ConditionField C_source() The Condition Field of: Field `source`
 */
class Media extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'farm' => new EntityFieldSignature(
                static::class, 'farm',
                new EntityRelation([
                    'relatedEntity' => \Entities\Farm::class,
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