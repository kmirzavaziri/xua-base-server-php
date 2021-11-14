<?php

namespace Xua\Core\Tools;

use Xua\Core\Eves\Entity;
use Xua\Core\Exceptions\DefinitionException;
use Xua\Core\Exceptions\EntityFieldException;
use Xua\Core\Services\ConstantService;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\FileInstanceSame;
use Xua\Core\Supers\Files\Generic;
use Xua\Core\Supers\Highers\Nullable;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Special\EntityFieldScheme;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Tools\Signature\Signature;

class SignatureValueCalculator
{
    /**
     * @param Entity $entity
     * @param EntityFieldScheme $scheme
     * @param mixed $value
     * @throws DefinitionException
     * @throws EntityFieldException
     */
    public static function setEntityField(Entity $entity, EntityFieldScheme $scheme, mixed $value): void
    {
        switch ($scheme->mode) {
            case EntityFieldScheme::MODE_TREE:
                self::setEntityFieldRecursive($entity, $scheme->tree, $value);
                return;
            case EntityFieldScheme::MODE_INSTANT:
                $scheme->instant['setter']($entity, $value);
                return;
        }
    }

    /**
     * @param Entity $entity
     * @param array $tree
     * @param mixed $value
     * @throws DefinitionException
     * @throws EntityFieldException
     */
    private static function setEntityFieldRecursive(Entity $entity, array $tree, mixed $value): void
    {
        $root = Signature::_(array_key_first($tree));
        if (is_a($root->declaration, EntityRelation::class)) {
            if ($root->declaration->toMany) {
                $result = [];
                foreach ($value as $index => $item) {
                    try {
                        $result[] = self::setRelative($entity, $tree, $item);
                    } catch (EntityFieldException $e) {
                        throw (new EntityFieldException)->setError($root->name, [$index => $e->getErrors()]);
                    }
                }
                $entity->{$root->name} = $result;
            } else {
                try {
                    $entity->{$root->name} = self::setRelative($entity, $tree, $value);
                } catch (EntityFieldException $e) {
                    throw (new EntityFieldException)->setError($root->name, $e->getErrors());
                }
            }
        } elseif (is_a($root->declaration, Generic::class)) {
            if (!is_a($value, FileInstanceSame::class)) {
                if ($entity->{$root->name} and file_exists($entity->{$root->name}->path)) {
                    unlink($entity->{$root->name}->path);
                }
                /** @noinspection PhpUndefinedMethodInspection */
                $value?->store(ConstantService::get('config', 'paths.storage') . DIRECTORY_SEPARATOR . $entity::table() . DIRECTORY_SEPARATOR . $entity->id);
                $entity->{$root->name} = $value;
            }
        } else {
            $entity->{$root->name} = $value;
        }
    }

    /**
     * @throws DefinitionException
     * @throws EntityFieldException
     */
    private static function setRelative(Entity $entity, array $tree, null|array|int $value): ?Entity
    {
        $root = Signature::_(array_key_first($tree));
        /** @var EntityRelation $relation */
        $relation = $root->declaration;

        if (is_int($value)) { // case: ID
            if ($relation->relation == EntityRelation::REL_R11O) {
                $return = new ($relation->relatedEntity)($value);
                if (!$return->id) {
                    throw (new EntityFieldException())->setError('id', ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                        'entity' => ExpressionService::get('entityclass.' . $relation->relatedEntity::table()),
                        'id' => $value,
                    ]));
                }
                if ($return->{$relation->invName}) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    throw new EntityFieldException(ExpressionService::get('errormessage.related.entity.with.id.id.is.already.in.rel', [
                        'entityLeft' => ExpressionService::get('entityclass.' . $root->class::table()),
                        'entityRight' => ExpressionService::get('entityclass.' . $relation->relatedEntity::table()),
                        'id' => $value,
                    ]));
                }
                return $return;
            } elseif ($relation->relation == EntityRelation::REL_R11R) {
                throw (new DefinitionException())->setError($root->name, ExpressionService::get('errormessage.cannot.change.R11R.by.id'));
            } else {
                $return = new ($relation->relatedEntity)($value);
                if (!$return->id) {
                    throw (new EntityFieldException())->setError('id', ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                        'entity' => ExpressionService::get('entityclass.' . $root->declaration->relatedEntity::table()),
                        'id' => $value,
                    ]));
                }
                return $return;
            }
        } elseif (is_array($value)) { // case: DATA
            $childrenJungle = $tree[$root->fullName];
            $children = self::getJungleRoots($childrenJungle);
            $hasId = in_array('id', array_map(function (Signature $child) { return $child->name; }, $children));
            if ($relation->toOne) {
                if ($hasId) {
                    throw (new DefinitionException())->setError($root->name, ExpressionService::get('errormessage.toOne.fields.cannot.include.id'));
                }
                $return = $entity->{$root->name};
                foreach ($children as $child) {
                    self::setEntityFieldRecursive($return, [$child->fullName => $childrenJungle[$child->fullName]], $value[$child->name]);
                }
                return $return;
            } else {
                if (!$hasId) {
                    throw (new DefinitionException())->setError($root->name, ExpressionService::get('errormessage.toMany.fields.must.include.id'));
                }
                $return = new ($relation->relatedEntity)($value['id']);
                if ($value['id'] != $return->id) {
                    throw (new EntityFieldException())->setError('id', ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                        'entity' => ExpressionService::get('entityclass.' . $relation->relatedEntity::table()),
                        'id' => $value['id'],
                    ]));
                }
                foreach ($children as $child) {
                    if ($child->name != 'id') {
                        self::setEntityFieldRecursive($return, [$child->fullName => $childrenJungle[$child->fullName]], $value[$child->name]);
                    }
                }
                return $return;
            }
        } elseif (is_null($value)) { // case: NULL
            if ($relation->required) {
                throw new EntityFieldException(ExpressionService::get('errormessage.required.request.item.not.provided'));
            }
            return null;
        }
        throw (new DefinitionException('There is an error in Xua Core. This Exception should not be throw in any case.'));
    }

    /**
     * @param Entity $entity
     * @param EntityFieldScheme $scheme
     * @return mixed
     */
    public static function getEntityField(Entity $entity, EntityFieldScheme $scheme): mixed
    {
        return match ($scheme->mode) {
            EntityFieldScheme::MODE_TREE => self::getEntityFieldRecursive($entity, $scheme->tree),
            EntityFieldScheme::MODE_INSTANT => $scheme->instant['getter']($entity),
            default => null,
        };
    }

    /**
     * @param Entity $entity
     * @param array $tree
     * @return mixed
     */
    private static function getEntityFieldRecursive(Entity $entity, array $tree): mixed
    {
        $root = Signature::_(array_key_first($tree));
        if (is_a($root->declaration, EntityRelation::class)) {
            if ($root->declaration->toMany) {
                $return = [];
                foreach ($entity->{$root->name} as $item) {
                    $return[] = self::getRelative($item, $tree);
                }
                return $return;
            } else {
                return self::getRelative($entity->{$root->name}, $tree);
            }
        } else {
            return $entity->{$root->name};
        }
    }

    /**
     * @param Entity $entity
     * @param array $tree
     * @return array|int|null
     */
    private static function getRelative(Entity $entity, array $tree): null|array|int
    {
        if (!$entity->id) {
            return null;
        }

        $root = Signature::_(array_key_first($tree));
        $childrenJungle = $tree[$root->fullName];

        if ($childrenJungle) {
            $return = [];
            $children = self::getJungleRoots($childrenJungle);
            foreach ($children as $child) {
                $return[$child->name] = self::getEntityFieldRecursive($entity, [$child->fullName => $childrenJungle[$child->fullName]]);
            }
            return $return;
        } else {
            return $entity->id;
        }
    }

    /**
     * @param array $jungle
     * @return Signature[]
     */
    private static function getJungleRoots(array $jungle): array
    {
        $roots = [];
        foreach ($jungle as $rootName => $children) {
            $roots[] = Signature::_($rootName);
        }
        return $roots;
    }

    /**
     * @param string $rootName
     * @param array $children
     * @return array
     * @throws DefinitionException
     * @throws \Xua\Core\Exceptions\SuperValidationException
     */
    public static function signatureTreeRootAndType(string $rootName, array $children): array
    {
        $root = Signature::_($rootName);
        if ($root === null) {
            throw new DefinitionException('Unknown signature ' . $rootName);
        }
        if (is_a($root->declaration, EntityRelation::class)) {
            if ($children) {
                $structure = [];
                foreach ($children as $childName => $grandChildren) {
                    [$child, $childType] = self::signatureTreeRootAndType($childName, $grandChildren);
                    if ($root->declaration->relatedEntity != $child->class) {
                        throw new DefinitionException("Cannot append a child from entity {$child->declaration->class} to a relational field on entity {$root->declaration->relatedEntity}.");
                    }
                    $structure[$child->name] = $childType;
                }
                $type = new StructuredMap([StructuredMap::structure => $structure, StructuredMap::nullable => $root->declaration->nullable]);
            } else {
                $type = new Nullable([Nullable::type => Signature::_($root->declaration->relatedEntity::id)->declaration]);
            }
            $type = $root->declaration->toMany ? new Sequence([Sequence::type => $type, Sequence::nullable => $root->declaration->nullable]) : $type;

            return [$root, $type];
        } else {
            if ($children) {
                throw new DefinitionException("Cannot append children to a non-relational field $root->name.");
            }
            return [$root, $root->declaration];
        }
    }
}