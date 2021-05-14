<?php

namespace Methods\User;

use Supers\Basics\Strings\Text;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property string test
 */
class SendCode extends Method
{
    protected static function _request(): array
    {
        return array_merge(parent::_request(), [
            'emailOrPhone' => new MethodItemSignature(new Text([]), true, null, false)
        ]);
    }

    protected static function _response(): array
    {
        return array_merge(parent::_response(), [
            'test' => new MethodItemSignature(new Text([]), true, null, false)
        ]);
    }

    protected function execute(array $request): void
    {
        extract($request);
        /**
         **********************************************
         * @var string $emailOrPhone
         **********************************************
         */

        // @TODO send code
        $this->test = 'you entered ' . $emailOrPhone;
    }
}