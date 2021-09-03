<?php

namespace Methods\Developer\Sms;

use Services\JsonLogService;
use Services\XUA\ConstantService;
use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\Sequence;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

class RemoveAll extends Method
{

    public static function isPublic(): bool
    {
        return false;
    }

    protected function body(): void
    {
        JsonLogService::removeAll('sms');
    }
}