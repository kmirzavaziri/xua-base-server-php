<?php

namespace XUA\Tools\Entity;

use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

class EntityArray
{

    /**
     * @param array $feed
     * @param VarqueMethodFieldSignature[] $fields
     * @param EntityFieldSignature|null $association
     * @return array[]
     */
    public static function manyToArray(array $feed, array $fields, ?EntityFieldSignature $association = null): array
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