<?php


namespace XUA\Tools\Signature;


use XUA\Eves\Super;

final class EntityFieldSignature
{
    private array $params = [];

    public function __construct(
        public string $entity,
        public string $name,
        public Super $type,
        public $default = null,
    ) {}

    public function p(?array $params = null) : array|EntityFieldSignature
    {
        if ($params === null) {
            return $this->params;
        } else {
            $this->params = $params;
            return $this;
        }
    }
}