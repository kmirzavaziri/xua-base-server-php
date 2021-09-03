<?php

namespace Methods\Developer\Sms;

use Services\JsonLogService;
use Services\UserService;
use XUA\Method;

class RemoveAll extends Method
{
    protected function body(): void
    {
        JsonLogService::removeAll('sms');
    }

    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}