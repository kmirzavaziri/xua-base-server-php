<?php

namespace Methods\Abstraction;

use Services\UserService;
use XUA\VARQUE\MethodRemove;

abstract class RemoveOneAdmin extends MethodRemove
{
    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}