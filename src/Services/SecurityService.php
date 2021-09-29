<?php


namespace XUA\Services;


use XUA\Supers\Numerics\DecimalRange;
use XUA\Exceptions\InstantiationException;
use XUA\Eves\Service;

final class SecurityService extends Service
{
    private static bool $hasPrivateMethodAccess = false;
    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `FlagService`.');
    }

    protected static function _init(): void
    {
        self::$hasPrivateMethodAccess = (isset($_SERVER['HTTP_XUA_INTERNAL_URPI_KEY']) and $_SERVER['HTTP_XUA_INTERNAL_URPI_KEY'] == ConstantService::get('config/XUA/sec', 'urpikey'));
    }

    public static function verifyPrivateMethodAccess() : bool
    {
        return self::$hasPrivateMethodAccess;
    }

    public static function getRandomSalt(int $length): string
    {
        if (!(new DecimalRange(['min' => 1, 'max' => 100, 'fractionalLength' => 0]))->accepts($length)) {
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