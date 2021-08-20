<?php

namespace XUA\Tools\Entity;

use Exception;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use XUA\Entity;
use XUA\Super;
use XUA\Tools\Signature\EntityFieldSignature;

final class EntityFieldSignatureTree
{
    /**
     * @var EntityFieldSignatureTree[]
     */
    public array $children = [];

    public function __construct(
        public EntityFieldSignature $value
    ){}

    public function addChild(EntityFieldSignatureTree|EntityFieldSignature $child): self
    {
        if (is_a($child, EntityFieldSignature::class)) {
            $child = new self($child);
        }

        if (!is_a($this->value->type, EntityRelation::class)) {
            throw new Exception("Cannot append children to a non-relational field {$this->value->name}.");
        }

        if ($this->value->type->relatedEntity != $child->value->entity) {
            throw new Exception("Cannot append a child from entity {$child->value->entity} to a relational field on entity {$this->value->type->relatedEntity}.");
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

    public function type(): Super
    {
        if (is_a($this->value->type, EntityRelation::class)) {
            if ($this->children) {
                $structure = [];
                foreach ($this->children as $child) {
                    $structure[$child->value->name] = $child->type();
                }
                $type = new StructuredMap(['structure' => $structure]);
            } else {
                $type = $this->value->type->relatedEntity::F_id()->type;
            }
            return $this->value->type->relation[1] == 'N' ? new Sequence(['type' => $type]) : $type;
        } else {
            return $this->value->type;
        }
    }

    public function valueFromEntity(Entity $entity): mixed
    {
        if (is_a($this->value->type, EntityRelation::class)) {
            if ($this->value->type->relation[1] == 'N') {
                $return = [];
                foreach ($entity->{$this->value->name} as $item) {
                    $return[] = $this->oneItemValueFromEntity($item);
                }
                return $return;
            } else {
                return $this->oneItemValueFromEntity($entity->{$this->value->name});
            }
        } else {
            return $entity->{$this->value->name};
        }
    }

    private function oneItemValueFromEntity(Entity $entity): array|int
    {
        if ($this->children) {
            $return = [];
            foreach ($this->children as $child) {
                $return[$child->value->name] = $child->valueFromEntity($entity);
            }
            return $return;
        } else {
            return $entity->id;
        }
    }
}