<?php

namespace Services;

use Services\XUA\ExpressionService;
use Supers\Basics\Boolean;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Numerics\DecimalRange;
use Supers\Basics\Numerics\Integer;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use Supers\Basics\Trilean;
use XUA\Service;

abstract class SimpleTypeService extends Service
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_STRING = 'string';
    const TYPE_SEQUENCE = 'sequence';
    const TYPE_ENUM = 'enum';
    const TYPE_SET = 'set';
    const TYPE_DATETIME = 'dateTime';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPES = [
        self::TYPE_BOOLEAN,
        self::TYPE_INTEGER,
        self::TYPE_DECIMAL,
        self::TYPE_STRING,
        self::TYPE_SEQUENCE,
        self::TYPE_ENUM,
        self::TYPE_SET,
        self::TYPE_DATETIME,
        self::TYPE_DATE,
        self::TYPE_TIME,
    ];

    public static function validateTypeParams(?string $type, ?array $typeParams, null|string|array &$message): bool
    {
        if ($type === null) {
            return true;
        }

        $typeParamsStructure = [
            'nullable' => new Boolean([]),
        ];
        switch ($type) {
            case 'boolean':
            case 'dateTime':
            case 'date':
            case 'time':
                $typeParamsStructure = array_merge($typeParamsStructure, [
                ]);
                break;
            case 'integer':
                $typeParamsStructure = array_merge($typeParamsStructure, [
                    'min' => new Integer(['nullable' => true]),
                    'max' => new Integer(['nullable' => true]),
                ]);
                $typeParams['min'] = $typeParams['min'] ?? null;
                $typeParams['max'] = $typeParams['max'] ?? null;
                break;
            case 'decimal':
                $typeParamsStructure = array_merge($typeParamsStructure, [
                    'min' => new Decimal(['nullable' => true]),
                    'max' => new Decimal(['nullable' => true]),
                ]);
                $typeParams['min'] = $typeParams['min'] ?? null;
                $typeParams['max'] = $typeParams['max'] ?? null;
                break;
            case 'string':
            case 'sequence':
                $typeParamsStructure = array_merge($typeParamsStructure, [
                    'minLength' => new Integer(['nullable' => true, 'unsigned' => true]),
                    'maxLength' => new Integer(['nullable' => true, 'unsigned' => true]),
                ]);
                $typeParams['minLength'] = $typeParams['minLength'] ?? null;
                $typeParams['maxLength'] = $typeParams['maxLength'] ?? null;
                break;
            case 'enum':
                $typeParamsStructure = array_merge($typeParamsStructure, [
                    'values' => new Sequence(['nullable' => false, 'type' => new Text([]), 'minLength' => 1]),
                ]);
                break;
            case 'set':
                $typeParamsStructure = array_merge($typeParamsStructure, [
                    'minLength' => new Integer(['nullable' => true, 'unsigned' => true]),
                    'maxLength' => new Integer(['nullable' => true, 'unsigned' => true]),
                    'values' => new Sequence(['nullable' => false, 'type' => new Text([]), 'minLength' => 1]),
                ]);
                $typeParams['minLength'] = $typeParams['minLength'] ?? null;
                $typeParams['maxLength'] = $typeParams['maxLength'] ?? null;
                break;
            default:
                $message = ExpressionService::get('unknown.simple.type.type', ['type' => $type]);
                return false;
        }
        return (new StructuredMap(['nullable' => true, 'structure' => $typeParamsStructure]))->explicitlyAccepts($typeParams, $message);
    }

    public static function validateValue(?string $type, array $typeParams, mixed $value, null|string|array &$message): bool
    {
        if ($type === null) {
            return true;
        }

        switch ($type) {
            case 'boolean':
                if ($typeParams['nullable']) {
                    $super = new Trilean([]);
                } else {
                    $super = new Boolean([]);
                }
                break;
            case 'integer':
                $super = new DecimalRange(array_merge($typeParams, ['fractionalLength' => 0]));
                break;
            case 'decimal':
                $super = new DecimalRange(array_merge($typeParams, ['fractionalLength' => 4]));
                break;
            case 'string':
                $super = new Text($typeParams);
                break;
            case 'sequence':
                $super = new Sequence($typeParams);
                break;
            case 'enum':
                $super = new Enum($typeParams);
                break;
            case 'set':
            case 'dateTime':
            case 'date':
            case 'time':
            default:
                $message = ExpressionService::get('errormessage.not.implemented.yet');
                return false;
        }
        return $super->explicitlyAccepts($value, $message);
    }


}