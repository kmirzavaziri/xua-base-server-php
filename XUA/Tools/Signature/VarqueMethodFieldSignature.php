<?php

namespace XUA\Tools\Signature;

class VarqueMethodFieldSignature
{
    public function __construct(
        public EntityFieldSignature $signature,
        public bool $required,
        public $default = null,
        public bool $const = false,
    ) {}
}