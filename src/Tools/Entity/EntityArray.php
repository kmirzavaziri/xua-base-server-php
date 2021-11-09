<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Eves\Entity;
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
    public static function oneToArray(Entity $entity, array $schemes): array
    {
        $data = [];
        foreach ($schemes as $scheme) {
            $data[$scheme->name] = SignatureValueCalculator::getEntityField($entity, $scheme);
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
}