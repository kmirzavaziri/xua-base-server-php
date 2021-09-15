<?php

namespace Methods\Developer\Sms;

use Services\JsonLogService;
use Services\UserService;
use Services\XUA\Dev\Credentials;
use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\Sequence;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetAll extends Method
{
    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            'result' => new MethodItemSignature(new Sequence(['nullable' => true, 'type' => new Map([])]), true, null, false)
        ]);
    }

    protected function body(): void
    {
        $this->result = JsonLogService::getAll('sms');
    }

    protected function validations(): void
    {
        Credentials::verifyDeveloper($this->error);
    }
}