<?php

namespace Xua\Core\Tools\Entity;

use Closure;
use Xua\Core\Supers\Universal;
use Xua\Core\Eves\Entity;
use Xua\Core\Exceptions\DefinitionException;
use Xua\Core\Eves\Super;

class InstantSignature extends SignatureTree
{
    private string $name;
    private Closure $getter;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(string $name, callable $getter){
        $this->name = $name;
        $this->getter = Closure::fromCallable($getter);
    }

    public function addChild(SignatureTree|string $child): self
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

    public function valueFromRequest(mixed $value, Entity $entity): mixed
    {
        throw new DefinitionException('Cannot use EntityInstantFieldSignatureTree in Adjust and Update methods.');
    }
}