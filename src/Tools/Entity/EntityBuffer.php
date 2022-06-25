<?php

namespace Xua\Core\Tools\Entity;

use ReflectionObject;
use Throwable;
use Xua\Core\Eves\Entity;

final class EntityBuffer {
    /**
     * @var Entity[]
     */
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
     * @return void
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
        foreach ($this->entities as $entity) {
            $entityReflector = new ReflectionObject($entity);
            $methodReflector = $entityReflector->getMethod('storeQueries');
            $methodReflector->setAccessible(true);  // @TODO take a look at this.
                                                    //      https://wiki.php.net/rfc/make-reflection-setaccessible-no-op
                                                    //      wen may remove this line and also remove the local variable $methodReflector.
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