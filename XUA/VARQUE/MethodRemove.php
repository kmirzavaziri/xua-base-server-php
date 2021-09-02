<?php

namespace XUA\VARQUE;

use XUA\MethodEve;
use XUA\Tools\Signature\MethodItemSignature;

abstract class MethodRemove extends MethodEve
{
    # Finalize Eve Methods
    final protected static function requestSignaturesCalculator(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'id' => new MethodItemSignature(static::entity()::F_id()->type, true, null, false),
        ]);
    }

    final protected static function responseSignaturesCalculator(): array
    {
        return parent::responseSignaturesCalculator();
    }

    protected function body(): void
    {
        (new ($this->entity())($this->Q_id))->delete();
    }

    # New Overridable Methods
    protected static function entity(): string
    {
        return '';
    }
}