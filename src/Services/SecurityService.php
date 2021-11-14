<?php

namespace Xua\Core\Services;

use Xua\Core\Supers\Numerics\DecimalRange;
use Xua\Core\Eves\Service;

final class SecurityService extends Service
{
    private static bool $hasPrivateMethodAccess = false;

    private function __construct() {}

    protected static function _init(): void
    {
        self::$hasPrivateMethodAccess = (isset($_SERVER['HTTP_XUA_INTERNAL_URPI_KEY']) and $_SERVER['HTTP_XUA_INTERNAL_URPI_KEY'] == ConstantService::get('config', 'services.sec.urpiKey'));
    }

    public static function verifyPrivateMethodAccess() : bool
    {
        return self::$hasPrivateMethodAccess;
    }

    public static function getRandomSalt(int $length): string
    {
        if (!(new DecimalRange([DecimalRange::min => 1, DecimalRange::max => 100, DecimalRange::fractionalLength => 0]))->accepts($length)) {
            $length = 32;
        }
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randMax = strlen($chars) - 1;
        $result = [];
        for ($i = 0; $i < $length; $i++) {
            $result[] = $chars[rand(0, $randMax)];
        }
        return implode('', $result);
    }
}