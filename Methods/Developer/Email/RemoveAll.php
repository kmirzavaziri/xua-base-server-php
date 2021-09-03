<?php

namespace Methods\Developer\Email;

use Services\JsonLogService;
use Services\UserService;
use XUA\Method;

class RemoveAll extends Method
{
    protected function body(): void
    {
        JsonLogService::removeAll('email');
    }

    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}