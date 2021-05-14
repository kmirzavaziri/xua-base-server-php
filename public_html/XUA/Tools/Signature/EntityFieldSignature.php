<?php


namespace XUA\Tools\Signature;


use XUA\Super;

final class EntityFieldSignature
{
    private array $param = [];

    public function __construct(
        public string $entity,
        public string $name,
        public Super $type,
        public $default = null,
    ) {}

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