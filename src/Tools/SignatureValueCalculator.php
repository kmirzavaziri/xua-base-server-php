<?php

namespace Xua\Core\Tools;

use Xua\Core\Eves\Entity;
use Xua\Core\Eves\MethodEve;
use Xua\Core\Exceptions\DefinitionException;
use Xua\Core\Exceptions\EntityFieldException;
use Xua\Core\Services\ConstantService;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\FileInstanceSame;
use Xua\Core\Supers\Files\Generic;
use Xua\Core\Supers\Special\EntityFieldScheme;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Tools\Entity\CF;
use Xua\Core\Tools\Entity\Condition;

class SignatureValueCalculator
{
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // set /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param Entity $entity
     * @param mixed $value
     * @param EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     */
    public static function setEntityField(Entity $entity, mixed $value, EntityFieldScheme $scheme, ?MethodEve $method = null): void
    {
        switch ($scheme->mode) {
            case EntityFieldScheme::MODE_INSTANT:
                self::setEntityFieldInstant($entity, $value, $scheme, $method);
                return;
            case EntityFieldScheme::MODE_SIGNATURE:
                self::setEntityFieldSignature($entity, $value, $scheme, $method);
                return;
        }
    }

    /**
     * @param Entity $entity
     * @param mixed $value
     * @param \Xua\Core\Supers\Special\EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     */
    private static function setEntityFieldInstant(Entity $entity, mixed $value, EntityFieldScheme $scheme, ?MethodEve $method = null): void
    {
        $scheme->instant['setter']($entity, $value, $method);
    }

    /**
     * @param Entity $entity
     * @param mixed $value
     * @param \Xua\Core\Supers\Special\EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     */
    private static function setEntityFieldSignature(Entity $entity, mixed $value, EntityFieldScheme $scheme, ?MethodEve $method = null): void
    {
        if (is_a($scheme->signature->declaration, EntityRelation::class)) {
            if ($scheme->signature->declaration->toMany) {
                $result = [];
                foreach ($value as $index => $item) {
                    try {
                        $result[] = self::setRelative($entity, $item, $scheme, $method);
                    } catch (EntityFieldException $e) {
                        throw (new EntityFieldException)->setError("$scheme->name.$index", $e->getErrors());
                    }
                }
                $entity->{$scheme->signature->name} = $result;
            } else {
                try {
                    $entity->{$scheme->signature->name} = self::setRelative($entity, $value, $scheme, $method);
                } catch (EntityFieldException $e) {
                    throw (new EntityFieldException)->setError($scheme->name, $e->getErrors());
                }
            }
        } elseif (is_a($scheme->signature->declaration, Generic::class)) {
            if (!is_a($value, FileInstanceSame::class)) {
                // @TODO check here when migrating to CDN or something
                if ($entity->{$scheme->signature->name} and file_exists($entity->{$scheme->signature->name}->path)) {
                    unlink($entity->{$scheme->signature->name}->path);
                }
                $value?->store($entity::table() . DIRECTORY_SEPARATOR . $entity->id);
                $entity->{$scheme->signature->name} = $value;
            }
        } else {
            $entity->{$scheme->signature->name} = $value;
        }
    }

    /**
     * @param \Xua\Core\Eves\Entity $entity
     * @param array|int|null $value
     * @param \Xua\Core\Supers\Special\EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     * @return \Xua\Core\Eves\Entity|null
     */
    private static function setRelative(Entity $entity, mixed $value, EntityFieldScheme $scheme, ?MethodEve $method = null): ?Entity
    {
        /** @var EntityRelation $relation */
        $relation = $scheme->signature->declaration;

        if (is_scalar($value)) { // case: Identifier
            if ($relation->relation == EntityRelation::REL_R11O) {
                $return = $relation->relatedEntity::getOne(Condition::leaf(CF::_($scheme->identifierField->fullName), Condition::EQ, $value));
                if (!$return->id) {
                    throw (new EntityFieldException())->setError('id', ExpressionService::getXua('supers.special.entity_relation.error_message.entity_with_id_does_not_exist', [
                        'entity' => ExpressionService::get('entities.' . $relation->relatedEntity::table() . '.title.singular'),
                        'id' => $value,
                    ]));
                }
                if ($return->{$relation->invName}) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    throw new EntityFieldException(ExpressionService::getXua('tools.signature_value_calculator.error_message.related_entity_with_id_is_already_in_rel', [
                        'entityLeft' => ExpressionService::get('entities.' . $scheme->signature->class::table() . '.title.singular'),
                        'entityRight' => ExpressionService::get('entities.' . $relation->relatedEntity::table() . '.title.singular'),
                        'id' => $value,
                    ]));
                }
                return $return;
            } elseif ($relation->relation == EntityRelation::REL_R11R) {
                throw (new DefinitionException())->setError($scheme->name, ExpressionService::getXua('tools.signature_value_calculator.error_message.cannot_change_R11R_by_id'));
            } else {
                $return = $relation->relatedEntity::getOne(Condition::leaf(CF::_($scheme->identifierField->fullName), Condition::EQ, $value));
                if (!$return->id) {
                    throw (new EntityFieldException())->setError($scheme->identifierField->name, ExpressionService::getXua('supers.special.entity_relation.error_message.entity_with_id_does_not_exist', [
                        'entity' => ExpressionService::get('entities.' . $scheme->signature->declaration->relatedEntity::table() . '.title.singular'),
                        'id' => $value,
                    ]));
                }
                return $return;
            }
        } elseif (is_array($value)) { // case: DATA
            $hasIdentifier = in_array($scheme->identifierField->name, array_map(function (EntityFieldScheme $child) { return $child->name; }, $scheme->children));
            if ($relation->toOne) {
                if ($hasIdentifier) {
                    throw (new DefinitionException())->setError($scheme->name, ExpressionService::getXua('tools.signature_value_calculator.error_message.toOne_fields_cannot_include_id'));
                }
                $return = $entity->{$scheme->signature->name};
                foreach ($scheme->children as $child) {
                    self::setEntityField($return, $value[$child->name], $child, $method);
                }
                return $return;
            } else {
                if (!$hasIdentifier) {
                    throw (new DefinitionException())->setError($scheme->name, ExpressionService::getXua('tools.signature_value_calculator.error_message.toMany_fields_must_include_id'));
                }
                $return = ($scheme->signature->declaration->relatedEntity)::new(0);
                foreach ($entity->{$scheme->signature->name} as $item) {
                    if ($item->{$scheme->identifierField->name} == $value[$scheme->identifierField->name]) {
                        $return = $item;
                        break;
                    }
                }
                if (!$return->id) {
                    if ($scheme->identifierField->name == 'id' and $value[$scheme->identifierField->name]) {
                        throw (new EntityFieldException())->setError('id', ExpressionService::getXua('supers.special.entity_relation.error_message.entity_with_id_does_not_exist', [
                            'entity' => ExpressionService::get('entities.' . $scheme->signature->declaration->relatedEntity::table() . '.title.singular'),
                            'id' => $value[$scheme->identifierField->name],
                        ]));
                    }
                }
                foreach ($scheme->children as $child) {
                    if ($child->name != 'id') {
                        self::setEntityField($return, $value[$child->name], $child, $method);
                    }
                }
                return $return;
            }
        } elseif (is_null($value)) { // case: NULL
            if ($relation->required) {
                throw new EntityFieldException(ExpressionService::getXua('generic.error_message.required_field_not_provided'));
            }
            return null;
        }
        throw (new DefinitionException('There is an error in Xua Core. This Exception should not be throw in any case.'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // get /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param Entity $entity
     * @param EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     * @return mixed
     */
    public static function getEntityField(Entity $entity, EntityFieldScheme $scheme, ?MethodEve $method = null): mixed
    {
        return match ($scheme->mode) {
            EntityFieldScheme::MODE_INSTANT => self::getEntityFieldInstant($entity, $scheme, $method),
            EntityFieldScheme::MODE_SIGNATURE => self::getEntityFieldSignature($entity, $scheme, $method),
            default => null,
        };
    }

    /**
     * @param \Xua\Core\Eves\Entity $entity
     * @param \Xua\Core\Supers\Special\EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     * @return mixed
     */
    private static function getEntityFieldInstant(Entity $entity, EntityFieldScheme $scheme, ?MethodEve $method = null): mixed {
        if (!$entity->id) {
            return null;
        }
        return $scheme->instant['getter']($entity, $method);
    }

    /**
     * @param Entity $entity
     * @param \Xua\Core\Supers\Special\EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     * @return mixed
     */
    private static function getEntityFieldSignature(Entity $entity, EntityFieldScheme $scheme, ?MethodEve $method = null): mixed
    {
        if (is_a($scheme->signature->declaration, EntityRelation::class)) {
            if ($scheme->signature->declaration->toMany) {
                $return = [];
                foreach ($entity->{$scheme->signature->name} as $item) {
                    $return[] = self::getRelative($item, $scheme, $method);
                }
                return $return;
            } else {
                return self::getRelative($entity->{$scheme->signature->name}, $scheme, $method);
            }
        } else {
            return $entity->{$scheme->signature->name};
        }
    }

    /**
     * @param Entity|null $entity
     * @param \Xua\Core\Supers\Special\EntityFieldScheme $scheme
     * @param \Xua\Core\Eves\MethodEve|null $method
     * @return array|int|null
     */
    private static function getRelative(?Entity $entity, EntityFieldScheme $scheme, ?MethodEve $method = null): mixed
    {
        if (!$entity or !$entity->id) {
            return null;
        }

        if ($scheme->children) {
            $return = [];
            $childHasUnderscore = false;
            foreach ($scheme->children as $child) {
                if ($child->name == '_') {
                    $return = self::getEntityField($entity, $child, $method);
                    $childHasUnderscore = true;
                    break;
                }
            }

            if (!$childHasUnderscore) {
                foreach ($scheme->children as $child) {
                    $value = self::getEntityField($entity, $child, $method);
                    $return[$child->name] = $value;
                }
            }
            return $return;
        } else {
            return $entity->id;
        }
    }

}