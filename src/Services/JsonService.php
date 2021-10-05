<?php


namespace XUA\Services;


use XUA\Supers\Highers\Json;
use XUA\Supers\Highers\Map;
use XUA\Supers\Highers\Sequence;
use XUA\Supers\Highers\StructuredMap;
use XUA\Supers\Universal;
use XUA\Eves\Service;
use XUA\Eves\Super;

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
                $input[$key] = $value === null
                ? null
                : (is_a($itemType, Json::class)
                    ? static::marshalItems((array)$value, $itemType)
                    : $itemType->marshal($value)
                );
            }
        }
        return $input;
    }

    public static function unmarshalItems(?array $input, Super $type): ?array
    {
        if ($input === null) {
            return null;
        }

        foreach ($input as $key => $value) {
            $itemType = match (get_class($type)) {
                StructuredMap::class => $type->structure[$key] ?? null,
                Map::class => $type->valueType,
                Sequence::class => $type->type,
                default => new Universal([])
            };
            if ($itemType) {
                $input[$key] = is_a($itemType, Json::class) ? static::unmarshalItems($value, $itemType) : $itemType->unmarshal($value);
            }
        }
        return $input;
    }
}