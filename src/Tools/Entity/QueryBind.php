<?php

namespace XUA\Tools\Entity;


final class QueryBind {
    public function __construct(
        public string $entity,
        public string $query,
        public array $bind,
    ){}
}