<?php

namespace XUA\Tools\Entity;

use Closure;
use Supers\Basics\Universal;
use XUA\Entity;
use XUA\Exceptions\DefinitionException;
use XUA\Super;
use XUA\Tools\Signature\EntityFieldSignature;

class EntityInstantField extends EntityFieldSignatureTree
{
    private string $name;
    private Closure $getter;
    public function __construct(string $name, callable $getter){
        $this->name = $name;
        $this->getter = Closure::fromCallable($getter);
    }

    public function addChild(EntityFieldSignatureTree|EntityFieldSignature $child): self
    {
        throw new DefinitionException("Cannot append a child to an instant field.");
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): Super
    {
        return new Universal([]);
    }

    public function valueFromEntity(Entity $entity): mixed
    {
        return ($this->getter)($entity);
    }

    public function valueFromRequest(mixed $value): mixed
    {
        throw new DefinitionException('Cannot use EntityInstantFieldSignatureTree in Adjust and Update methods.');
    }
}