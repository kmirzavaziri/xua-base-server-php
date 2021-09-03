<?php

namespace Methods\Abstraction;

use Services\UserService;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\VARQUE\MethodRemove;

abstract class RemoveOneByIdAdmin extends MethodRemove
{
    final protected static function requestSignaturesCalculator(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'id' => new MethodItemSignature(static::entity()::F_id()->type, true, null, false),
        ]);
    }

    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }

    protected function feed(): Entity
    {
        return new ($this->entity())($this->Q_id);
    }
}