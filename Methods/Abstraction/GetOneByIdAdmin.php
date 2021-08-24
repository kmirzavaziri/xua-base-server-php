<?php

namespace Methods\Abstraction;

use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\VARQUE\MethodView;

abstract class GetOneByIdAdmin extends GetOneById
{
    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}