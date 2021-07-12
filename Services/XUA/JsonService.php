<?php


namespace Services\XUA;


use Supers\Basics\Highers\Json;
use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use XUA\Service;
use XUA\Super;

abstract class JsonService extends Service
{
    public static function marshalItems(array $input, Super $type): array
    {
        foreach ($input as $key => $value) {
            $itemType = match (get_class($type)) {
                StructuredMap::class => $type->structure[$key] ?? null,
                Map::class => $type->valueType,
                Sequence::class => $type->type,
            };
            if ($itemType) {
                $input[$key] = is_a($itemType, Json::class) ? static::marshalItems((array)$value, $itemType) : $itemType->marshal($value);
            }
        }
        return $input;
    }
    public static function unmarshalItems(array $input, Super $type): array
    {
        foreach ($input as $key => $value) {
            $itemType = match (get_class($type)) {
                StructuredMap::class => $type->structure[$key] ?? null,
                Map::class => $type->valueType,
                Sequence::class => $type->type,
            };
            if ($itemType) {
                $input[$key] = is_a($itemType, Json::class) ? static::unmarshalItems($value, $itemType) : $itemType->unmarshal($value);
            }
        }
        return $input;
    }
}