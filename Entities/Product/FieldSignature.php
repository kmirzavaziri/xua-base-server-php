<?php

namespace Entities\Product;

use Services\XUA\LocaleLanguage;
use Supers\Basics\Boolean;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Numerics\Integer;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Symbol;
use Supers\Basics\Strings\Text;
use Supers\Customs\Name;
use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property string key
 * @method static EntityFieldSignature F_key() The Signature of: Field `key`
 * @method static ConditionField C_key() The Condition Field of: Field `key`
 * @property string title
 * @method static EntityFieldSignature F_title() The Signature of: Field `title`
 * @method static ConditionField C_title() The Condition Field of: Field `title`
 * @property ?string type
 * @method static EntityFieldSignature F_type() The Signature of: Field `type`
 * @method static ConditionField C_type() The Condition Field of: Field `type`
 * @property ?array typeParams
 * @method static EntityFieldSignature F_typeParams() The Signature of: Field `typeParams`
 * @method static ConditionField C_typeParams() The Condition Field of: Field `typeParams`
 * @property \Entities\Product\Category category
 * @method static EntityFieldSignature F_category() The Signature of: Field `category`
 * @method static ConditionField C_category() The Condition Field of: Field `category`
 */
class FieldSignature extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'name' => new EntityFieldSignature(
                static::class, 'key',
                new Symbol(['nullable' => false, 'minLength' => 1, 'maxLength' => 200]),
                null
            ),
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'type' => new EntityFieldSignature(
                static::class, 'type',
                new Enum([
                    'nullable' => true,
                    'values' => ['boolean', 'integer', 'decimal', 'string', 'sequence', 'enum', 'set', 'dateTime', 'date', 'time', 'timeInterval']
                ]),
                null
            ),
            'typeParams' => new EntityFieldSignature(
                static::class, 'typeParams',
                new Map(['nullable' => true]),
                null
            ),
            'category' => new EntityFieldSignature(
                static::class, 'category',
                new EntityRelation([
                    'relatedEntity' => Category::class,
                    'relation' => 'NI',
                    'invName' => 'additionalFields',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'there',
                ]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
        ]);
    }

    protected function _validation(EntityFieldException $exception): void
    {
        if ($this->type !== null) {
            $typeParamsStructure = [
                'nullable' => new Boolean([]),
            ];
            $badType = false;
            switch ($this->type) {
                case 'boolean':
                case 'dateTime':
                case 'date':
                case 'time':
                case 'timeInterval':
                    $typeParamsStructure = array_merge($typeParamsStructure, [
                    ]);
                    break;
                case 'integer':
                    $typeParamsStructure = array_merge($typeParamsStructure, [
                        'min' => new Integer(['nullable' => true]),
                        'max' => new Integer(['nullable' => true]),
                    ]);
                    break;
                case 'decimal':
                    $typeParamsStructure = array_merge($typeParamsStructure, [
                        'min' => new Decimal(['nullable' => true]),
                        'max' => new Decimal(['nullable' => true]),
                    ]);
                    break;
                case 'string':
                case 'sequence':
                    $typeParamsStructure = array_merge($typeParamsStructure, [
                        'minLength' => new Integer(['unsigned' => true, 'nullable' => true]),
                        'maxLength' => new Integer(['unsigned' => true, 'nullable' => true]),
                    ]);
                    break;
                case 'enum':
                    $typeParamsStructure = array_merge($typeParamsStructure, [
                        'values' => new Sequence(['type' => new Text([]), 'minLength' => 1]),
                    ]);
                    break;
                case 'set':
                    $typeParamsStructure = array_merge($typeParamsStructure, [
                        'minLength' => new Integer(['unsigned' => true, 'nullable' => true]),
                        'maxLength' => new Integer(['unsigned' => true, 'nullable' => true]),
                        'values' => new Sequence(['type' => new Text([]), 'minLength' => 1]),
                    ]);
                    break;
                default:
                    $badType = true;
            }
            if (!$badType and !(new StructuredMap(['structure' => $typeParamsStructure]))->accepts($this->typeParams, $message)) {
                $exception->setError('typeParams', $message);
            }
        }
    }
}