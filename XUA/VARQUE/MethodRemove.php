<?php

namespace XUA\VARQUE;

use XUA\Entity;
use XUA\MethodEve;

abstract class MethodRemove extends MethodEve
{
    # Finalize Eve Methods
    final protected static function responseSignaturesCalculator(): array
    {
        return parent::responseSignaturesCalculator();
    }

    protected function body(): void
    {
        $this->feed()->delete();
    }

    # New Overridable Methods
    protected static function entity(): string
    {
        return '';
    }

    abstract protected function feed(): Entity;
}