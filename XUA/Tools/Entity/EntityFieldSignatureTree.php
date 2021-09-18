<?php

namespace XUA\Tools\Entity;

use Services\XUA\ExpressionService;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use XUA\Entity;
use XUA\Exceptions\DefinitionException;
use XUA\Exceptions\EntityFieldException;
use XUA\Super;
use XUA\Tools\Signature\EntityFieldSignature;

class EntityFieldSignatureTree
{
    /**
     * @var EntityFieldSignatureTree[]
     */
    public array $children = [];

    public function __construct(
        private ?EntityFieldSignature $value
    ){}

    public function addChild(EntityFieldSignatureTree|EntityFieldSignature $child): self
    {
        if (is_a($child, EntityFieldSignature::class)) {
            $child = new self($child);
        }

        if (!is_a($this->value->type, EntityRelation::class)) {
            throw new DefinitionException("Cannot append children to a non-relational field {$this->name()}.");
        }

        if ($this->value->type->relatedEntity != $child->value->entity) {
            throw new DefinitionException("Cannot append a child from entity {$child->value->entity} to a relational field on entity {$this->value->type->relatedEntity}.");
        }

        $this->children[] = $child;
        return $this;
    }

    public function addChildren(array $children): self
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
        return $this;
    }

    public function name(): string
    {
        return $this->value->name;
    }

    public function type(): Super
    {
        if (is_a($this->value->type, EntityRelation::class)) {
            if ($this->children) {
                $structure = [];
                foreach ($this->children as $child) {
                    $structure[$child->name()] = $child->type();
                }
                $type = new StructuredMap(['structure' => $structure]);
            } else {
                $type = $this->value->type->relatedEntity::F_id()->type;
            }
            $type = $this->value->type->toMany ? new Sequence(['type' => $type]) : $type;
            $type->nullable = $this->value->type->nullable;
            return $type;
        } else {
            return $this->value->type;
        }
    }

    public function valueFromEntity(Entity $entity): mixed
    {
        if (is_a($this->value->type, EntityRelation::class)) {
            if ($this->value->type->toMany) {
                $return = [];
                foreach ($entity->{$this->name()} as $item) {
                    $return[] = $this->oneItemValueFromEntity($item);
                }
                return $return;
            } else {
                return $this->oneItemValueFromEntity($entity->{$this->name()});
            }
        } else {
            return $entity->{$this->name()};
        }
    }

    public function valueFromRequest(mixed $value): mixed
    {
        if (is_a($this->value->type, EntityRelation::class)) {
            if ($this->value->type->toMany) {
                $return = [];
                foreach ($value as $index => $item) {
                    try {
                        $return[] = $this->oneItemValueFromRequest($item);
                    } catch (EntityFieldException $e) {
                        throw (new EntityFieldException)->setError($this->name(), [$index => $e->getErrors()]);
                    }
                }
                return $return;
            } else {
                try {
                    return $this->oneItemValueFromRequest($value);
                } catch (EntityFieldException $e) {
                    throw (new EntityFieldException)->setError($this->name(), $e->getErrors());
                }
            }
        } else {
            return $value;
        }
    }


    private function oneItemValueFromEntity(Entity $entity): null|array|int
    {
        if (!$entity->id) {
            return null;
        }

        if ($this->children) {
            $return = [];
            foreach ($this->children as $child) {
                $return[$child->name()] = $child->valueFromEntity($entity);
            }
            return $return;
        } else {
            return $entity->id;
        }
    }

    private function oneItemValueFromRequest(array|int $value): entity
    {
        if (is_int($value)) {
            $return = new ($this->value->type->relatedEntity)($value);
            if ($value != $return->id) {
                throw (new EntityFieldException())->setError('id', ExpressionService::get('errormessage.invalid.id.id', ['id' => $value]));
            }
            return $return;
        }

        if (in_array('id', array_map(function (EntityFieldSignatureTree $tree) { return $tree->name(); }, $this->children))) {
            $return = new ($this->value->type->relatedEntity)($value['id']);
            if ($value['id'] != $return->id) {
                throw (new EntityFieldException())->setError('id', ExpressionService::get('errormessage.invalid.id.id', ['id' => $value['id']]));
            }
            foreach ($this->children as $child) {
                if ($child->name() != 'id') {
                    $return->{$child->name()} = $child->valueFromRequest($value[$child->name()]);
                }
            }
            return $return;
        } else {
            throw (new DefinitionException())->setError($this->name(), 'All EntityRelation fields must include id.');
        }
    }
}