<?php


namespace XUA\Tools\Entity;


use Exception;
use Supers\Basics\EntitySupers\EntityRelation;
use XUA\Exceptions\EntityConditionException;
use XUA\Tools\Signature\EntityFieldSignature;

final class ConditionField
{
    private array $joins = [];
    private string $alias;

    public function __construct(public EntityFieldSignature $signature) {
        $this->alias = $this->signature->entity::table();
    }

    public function rel(EntityFieldSignature $signature): static
    {
        if (!is_a($this->signature->type, EntityRelation::class)) {
            throw (new EntityConditionException())->setError($signature->name, 'Cannot relate on non-relational field.');
        }
        if ($signature->entity != $this->signature->type->relatedEntity) {
            throw (new EntityConditionException())->setError($signature->name, 'Expected a field in ' . $this->signature->type->relatedEntity . ', got a field in ' . $signature->entity . '.');
        }

        $join = new Join(Join::LEFT, $this->alias, $this->signature);
        $this->joins[] = $join;
        $this->alias = $join->rightTableNameAlias();
        $this->signature = $signature;

        return $this;
    }

    public function name() : string
    {
        return $this->alias . '.' . $this->signature->name;
    }

    public function joins(): array
    {
        return $this->joins;
    }
}