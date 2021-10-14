<?php

namespace XUA\Tools\Entity;


use ReflectionObject;
use Throwable;
use XUA\Eves\Entity;
use XUA\Exceptions\EntityException;
use XUA\Exceptions\EntityFieldException;

final class EntityBuffer {
    /**
     * @var Entity[]
     */
    private array $entities = [];

    public function add(Entity $entity): self
    {
        $this->entities[] = $entity;
        return $this;
    }

    /**
     * @throws Throwable
     * @throws EntityException
     * @throws EntityFieldException
     */
    public function store(): void
    {
        $savePoint = Entity::savePoint();
        try {
            $this->_x_store();
        } catch (Throwable $t) {
            Entity::rollbackToSavepoint($savePoint);
            throw $t;
        }
    }

    private function _x_store(): void
    {
        $queryString = '';
        $bind = [];
        foreach ($this->entities as $entity) {
            $entityReflector = new ReflectionObject($entity);
            $methodReflector = $entityReflector->getMethod('storeQueries');
            $methodReflector->setAccessible(true);
            /** @var Query[] $queries */
            $queries = $methodReflector->invoke($entity);
            foreach ($queries as $query) {
                $queryString .= $query->query . ';';
                $bind = array_merge($bind, $query->bind);
            }
        }
        if ($queryString) {
            Entity::execute($queryString, $bind);
        }
    }
}