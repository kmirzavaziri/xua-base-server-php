<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Eves\Entity;
use Xua\Core\Eves\MethodEve;
use Xua\Core\Eves\Super;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Supers\Highers\Map;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Special\EntityFieldScheme;
use Xua\Core\Tools\Signature\Signature;
use Xua\Core\Tools\SignatureValueCalculator;

class EntityArray
{
    /**
     * @param Entity $entity
     * @param EntityFieldScheme[] $schemes
     * @return array
     */
    public static function oneToArray(Entity $entity, array $schemes, ?MethodEve $method = null): array
    {
        $data = [];
        foreach ($schemes as $scheme) {
            $data[$scheme->name] = SignatureValueCalculator::getEntityField($entity, $scheme, $method);
        }
        return $data;
    }

    /**
     * @param Entity[] $feed
     * @param EntityFieldScheme[] $schemes
     * @param Signature|null $association
     * @return array[]
     */
    public static function manyToArray(array $feed, array $schemes, ?Signature $association = null): array
    {
        $result = [];
        foreach ($feed as $entity) {
            $data = self::oneToArray($entity, $schemes);
            if ($association) {
                $result[$entity->{$association->name}] = $data;
            } else {
                $result[] = $data;
            }
        }

        return $result;
    }

    /**
     * @param EntityFieldScheme[] $schemes
     * @return Super
     * @throws SuperValidationException
     */
    public static function oneToArrayType(array $schemes): Super
    {
        $structure = [];
        foreach ($schemes as $scheme) {
            $structure[$scheme->name] = $scheme->type;
        }
        return new StructuredMap([StructuredMap::structure => $structure]);
    }

    /**
     * @param EntityFieldScheme[] $schemes
     * @param Super|null $association
     * @return Super
     * @throws SuperValidationException
     */
    public static function manyToArrayType(array $schemes, ?Super $association = null): Super
    {
        $itemType = self::oneToArrayType($schemes);
        return $association
            ? new Map([Map::keyType => $association, Map::valueType => $itemType])
            : new Sequence([Sequence::type => $itemType]);

    }
}