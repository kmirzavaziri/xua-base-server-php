<?php

namespace Xua\Core\Tools\Entity;

use ReflectionClass;
use ReflectionObject;
use Throwable;
use Xua\Core\Eves\Entity;

final class EntityBuffer {
    /**  @var Entity[] */
    private array $entities = [];
    private static ?EntityBuffer $efficientBuffer = null;

    public static function getEfficientBuffer(): self {
        if (!self::$efficientBuffer) {
            self::$efficientBuffer = new self();
        }
        return self::$efficientBuffer;
    }

    public function add(Entity $entity): self
    {
        $this->entities[] = $entity;
        return $this;
    }

    /**
     * @param Entity[] $entities
     * @return $this
     */
    public function addMany(array $entities): self
    {
        foreach ($entities as $entity) {
            $this->add($entity);
        }
        return $this;
    }

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
        $entityClassReflector = new ReflectionClass(Entity::class);
        $entityClassReflector->setStaticPropertyValue('_x_entities_visited_for_store', []);
        foreach ($this->entities as $entity) {
            $entityReflector = new ReflectionObject($entity);
            $storeQueriesReflector = $entityReflector->getMethod('storeQueries');
            /** @var Query[] $queries */
            $queries = $storeQueriesReflector->invoke($entity);
            foreach ($queries as $query) {
                $queryString .= $query->query . ';';
                $bind = array_merge($bind, $query->bind);
            }
        }
        $entityClassReflector->setStaticPropertyValue('_x_entities_visited_for_store', []);
        if ($queryString) {
            Entity::execute($queryString, $bind);
        }
    }
}