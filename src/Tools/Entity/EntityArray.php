<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Tools\Signature\Signature;
use Xua\Core\Tools\Signature\VarqueMethodFieldSignature;

class EntityArray
{

    /**
     * @param array $feed
     * @param VarqueMethodFieldSignature[] $fields
     * @param Signature|null $association
     * @return array[]
     */
    public static function manyToArray(array $feed, array $fields, ?Signature $association = null): array
    {
        $result = [];
        foreach ($feed as $entity) {
            $data = [];
            foreach ($fields as $field) {
                $data[$field->root->name()] = $field->root->valueFromEntity($entity);
            }
            if ($association) {
                $result[$entity->{$association->name}] = $data;
            } else {
                $result[] = $data;
            }
        }

        return $result;
    }
}