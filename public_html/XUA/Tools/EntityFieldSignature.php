<?php


namespace XUA\Tools;


use XUA\Exceptions\EntityFieldException;
use XUA\Super;

final class EntityFieldSignature
{
    public EntityRelObject $rel;
    private array $param = [];

    public function __construct(
        public string $entity,
        public string $name,
        public Super $type,
        public $default = null,
    )
    {
        $this->rel = new EntityRelObject($this->name);
    }

    public function name() : string
    {
        return $this->entity::table() . '.' . $this->name;
    }

    public function p(?array $param = null) : array|EntityFieldSignature
    {
        if ($param === null) {
            return $this->param;
        } else {
            $this->param = $param;
            return $this;
        }
    }
}