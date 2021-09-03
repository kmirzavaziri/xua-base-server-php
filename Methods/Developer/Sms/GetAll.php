<?php

namespace Methods\Developer\Sms;

use Services\JsonLogService;
use Services\XUA\ConstantService;
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

    public static function isPublic(): bool
    {
        return false;
    }

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
}