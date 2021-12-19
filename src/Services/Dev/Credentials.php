<?php

namespace Xua\Core\Services\Dev;

use Xua\Core\Services\ConstantService;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Exceptions\MethodRequestException;
use Xua\Core\Eves\Service;

final class Credentials extends Service
{
    private static ?string $developer = null;

    public static function developer(): ?string
    {
        return self::$developer;
    }

    private function __construct() {}

    protected static function _init(): void
    {
        if (isset($_SERVER['HTTP_XUA_DEV_CREDENTIALS'])){
            $headerStringValue = explode(':', $_SERVER['HTTP_XUA_DEV_CREDENTIALS']);
            $devs = ConstantService::get('config', 'services.sec.devs');
            if (count($headerStringValue) == 2
                and isset($devs[$headerStringValue[0]])
                and password_verify($headerStringValue[1], $devs[$headerStringValue[0]])
            ) {
                self::$developer = $headerStringValue[0];
            }
        }
    }

    public static function verifyDeveloper(MethodRequestException $error): string
    {
        $developer = self::$developer;
        if (!$developer) {
            throw ($error ?? new MethodRequestException())->setError('', ExpressionService::getXua('generic.error_message.access_denied'));
        }
        return $developer;
    }

}