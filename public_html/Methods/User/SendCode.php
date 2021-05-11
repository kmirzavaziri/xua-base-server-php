<?php

namespace Methods\User;

use Supers\Basics\Strings\Text;
use XUA\Method;
use XUA\Tools\MethodItemSignature;

class SendCode extends Method
{
    protected static function request(): array
    {
        return array_merge(parent::request(), [
            'emailOrPhone' => new MethodItemSignature(new Text([]), true, null, false)
        ]);
    }

    protected static function response(): array
    {
        return array_merge(parent::request(), [
        ]);
    }

    protected function execute(array $request): void
    {
        // @TODO send code
    }
}