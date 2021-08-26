<?php

namespace Methods\Abstraction;

use Services\UserService;
use XUA\VARQUE\MethodQuery;

abstract class GetAllAdmin extends MethodQuery
{
    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}