<?php

namespace Methods\User;

use Supers\Basics\Strings\Text;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property string Q_emailOrPhone
 * @method static MethodItemSignature Q_emailOrPhone() The Signature of: Request Item `emailOrPhone`
 * @property string test
 * @method static MethodItemSignature R_test() The Signature of: Response Item `test`
 */
class SendCode extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'emailOrPhone' => new MethodItemSignature(new Text([]), true, null, false)
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
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