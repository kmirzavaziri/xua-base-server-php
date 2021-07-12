<?php


namespace Services\XUA\Entity;

use Exception;
use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use XUA\Entity;
use XUA\Exceptions\InstantiationException;
use XUA\Exceptions\SuperValidationException;
use XUA\Service;
use XUA\Super;
use XUA\Tools\Signature\EntityFieldSignature;

final class EntityExtractService extends Service
{
    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `ConstantService`.');
    }

    /**
     * @param Entity $entity
     * @param EntityFieldSignature $field
     * @return mixed
     * @throws Exception
     */
    public static function field(Entity $entity, EntityFieldSignature $field): mixed
    {
        self::validateFieldsOnEntity($entity::class, [$field]);
        return $entity->{$field->name};
    }

    /**
     * @param Entity $entity
     * @param EntityFieldSignature[] $fields
     * @return array
     * @throws Exception
     */
    public static function fields(Entity $entity, array $fields): array
    {
        return self::fieldsArray([$entity], $fields, null)[0];
    }

    /**
     * @param Entity[] $entities
     * @param EntityFieldSignature $field
     * @param EntityFieldSignature|null $associationField
     * @return array
     * @throws Exception
     */
    public static function fieldArray(array $entities, EntityFieldSignature $field, ?EntityFieldSignature $associationField = null): array
    {
        return array_map(function (array $entityData) use ($field) {
            return $entityData[$field->name];
        }, self::fieldsArray($entities, [$field], $associationField));
    }


    /**
     * @param Entity[] $entities
     * @param EntityFieldSignature[] $fields
     * @param EntityFieldSignature|null $associationField
     * @return array[]
     * @throws Exception
     */
    public static function fieldsArray(array $entities, array $fields, ?EntityFieldSignature $associationField = null): array
    {
        $fieldsToValidate = $associationField ? [...$fields, $associationField] : $fields;
        $validatedEntities = [];
        foreach ($entities as $entity) {
            if (!isset($validatedEntities[$entity::class])) {
                self::validateFieldsOnEntity($entity::class, $fieldsToValidate);
                $validatedEntities[$entity::class] = true;
            }
        }

        $entitiesData = [];
        foreach ($entities as $entity) {
            $entityData = [];
            foreach ($fields as $field) {
                $entityData[$field->name] = $entity->{$field->name};
            }
            if ($associationField) {
                $entitiesData[$entity->{$associationField->name}] = $entityData;
            } else {
                $entitiesData[] = $entityData;
            }
        }
        return $entitiesData;
    }

    /**
     * @param string $entityName
     * @param EntityFieldSignature[] $fields
     * @throws Exception
     */
    private static function validateFieldsOnEntity(string $entityName, array $fields): void
    {
        foreach ($fields as $field) {
            if ($field->entity != $entityName) {
                throw new Exception("Field's entity '$field->entity' is not equal to '$entityName'.");
            }
            if (!in_array($field->name, array_keys($entityName::fieldSignatures()))) {
                throw new Exception("Field '$field->name' does not exist in entity '$field->entity'.");
            }
        }
    }

    /**
     * @param EntityFieldSignature $field
     * @return mixed
     */
    public static function fieldType(EntityFieldSignature $field): Super
    {
        return $field->type;
    }

    /**
     * @param EntityFieldSignature[] $fields
     * @return mixed
     * @throws SuperValidationException
     */
    public static function fieldsType(array $fields): Super
    {
        $structure = [];
        foreach ($fields as $field) {
            $structure[$field->name] = $field->type;
        }
        return new StructuredMap(['structure' => $structure]);
    }

    /**
     * @param EntityFieldSignature $field
     * @param EntityFieldSignature|null $associationField
     * @return Super
     */
    public static function fieldArrayType(EntityFieldSignature $field, ?EntityFieldSignature $associationField = null): Super
    {
        return $associationField
            ? new Map(['keyType' => $associationField->type, 'valueType' => $field->type])
            : new Sequence(['type' => $field->type]);
    }


    /**
     * @param EntityFieldSignature[] $fields
     * @param EntityFieldSignature|null $associationField
     * @return Super
     * @throws SuperValidationException
     */
    public static function fieldsArrayType(array $fields, ?EntityFieldSignature $associationField = null): Super
    {
        return $associationField
            ? new Map(['keyType' => $associationField->type, 'valueType' => self::fieldsType($fields)])
            : new Sequence(['type' => self::fieldsType($fields)]);
    }

}