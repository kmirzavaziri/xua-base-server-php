<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Supers\Special\DatabaseVirtualField;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Exceptions\EntityConditionException;
use Xua\Core\Tools\Signature\Signature;

final class CF
{
    private array $joins = [];
    private string $alias;

    public Signature $signature;

    private function __construct() {
    }

    public static function _(string $signatureName): self
    {
        $instance = new self();
        $instance->signature = Signature::_($signatureName);
        /** @noinspection PhpUndefinedMethodInspection */
        $instance->alias = $instance->signature->class::table();
        return $instance;
    }

    public function rel(string $signatureName): self
    {
        $signature = Signature::_($signatureName);
        if (!is_a($this->signature->declaration, EntityRelation::class)) {
            throw (new EntityConditionException())->setError($signature->name, 'Cannot relate on non-relational field.');
        }
        if ($signature->class != $this->signature->declaration->relatedEntity) {
            throw (new EntityConditionException())->setError($signature->name, 'Expected a field in ' . $this->signature->declaration->relatedEntity . ', got a field in ' . $signature->class . '.');
        }

        $join = new Join(Join::LEFT, $this->alias, $this->signature);
        $this->joins[] = $join;
        $this->alias = $join->rightTableNameAlias();
        $this->signature = $signature;

        return $this;
    }

    public function name() : string
    {
        if (is_a($this->signature->declaration, DatabaseVirtualField::class)) {
            return "`{$this->alias}__{$this->signature->name}`";
        } else {
            return "`$this->alias`.`{$this->signature->name}`";
        }
    }

    public function joins(): array
    {
        return $this->joins;
    }
}