<?php

namespace XUA\Tools\Entity;

use Exception;
use Services\XUA\ExpressionService;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use XUA\Entity;
use XUA\Exceptions\DefinitionException;
use XUA\Exceptions\MethodRequestException;
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

    public function valueFromRequest(mixed $value, string $keyName): mixed
    {
        if (is_a($this->value->type, EntityRelation::class)) {
            if ($this->value->type->relation[1] == 'N') {
                $return = [];
                foreach ($value as $item) {
                    $return[] = $this->oneItemValueFromRequest($item, $keyName);
                }
                return $return;
            } else {
                return $this->oneItemValueFromRequest($value, $keyName);
            }
        } else {
            return $value;
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

    private function oneItemValueFromRequest(array $value, string $keyName): entity
    {
        if (in_array('id', array_map(function (EntityFieldSignatureTree $tree) { return $tree->value->name; }, $this->children))) {
            $return = new ($this->value->type->relatedEntity)($value['id']);
            if ($value['id'] != $return->id) {
                throw ($error ?? new MethodRequestException())->setError($keyName, ExpressionService::get('errormessage.invalid.id'));
            }
            foreach ($this->children as $child) {
                if ($child->value->name != 'id') {
                    $return->{$child->value->name} = $child->valueFromRequest($value[$child->value->name], $keyName);
                }
            }
            return $return;
        } else {
            throw (new DefinitionException())->setError($this->value->name, 'All EntityRelation fields must include id.');
        }
    }
}