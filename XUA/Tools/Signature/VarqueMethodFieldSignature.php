<?php

namespace XUA\Tools\Signature;

class VarqueMethodFieldSignature
{
    public function __construct(
        public EntityFieldSignature $signature,
        public bool $required,
    ) {}
}