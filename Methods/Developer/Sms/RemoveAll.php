<?php

namespace Methods\Developer\Sms;

use Services\JsonLogService;
use Services\UserService;
use Services\XUA\Dev\Credentials;
use XUA\Method;

class RemoveAll extends Method
{
    protected function body(): void
    {
        JsonLogService::removeAll('sms');
    }

    protected function validations(): void
    {
        Credentials::verifyDeveloper($this->error);
    }
}