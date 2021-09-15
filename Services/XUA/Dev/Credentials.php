<?php


namespace Services\XUA\Dev;


use Exception;
use Services\XUA\ExpressionService;
use XUA\Exceptions\InstantiationException;
use XUA\Exceptions\MethodRequestException;
use XUA\Service;

final class Credentials extends Service
{
    private const DEVS = [
        'kamyar' => '$2y$10$Q0ESdY25efJwj4Yd4H.Lw.sy2x.qRD5MGsh/v5y3/ZOT/5GLe8MJS',
    ];

    private static ?string $developer = null;

    public static function developer(): ?string
    {
        return self::$developer;
    }

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `Credentials`.');
    }

    protected static function _init(): void
    {
        if (isset($_SERVER['HTTP_XUA_DEV_CREDENTIALS'])){
            $headerStringValue = explode(':', $_SERVER['HTTP_XUA_DEV_CREDENTIALS']);
            if (count($headerStringValue) == 2
                and isset(Credentials::DEVS[$headerStringValue[0]])
                and password_verify($headerStringValue[1], Credentials::DEVS[$headerStringValue[0]])
            ) {
                self::$developer = $headerStringValue[0];
            }
        }
    }

    public static function verifyDeveloper(MethodRequestException $error): string
    {
        $developer = self::$developer;
        if (!$developer) {
            throw ($error ?? new MethodRequestException())->setError('', ExpressionService::get('errormessage.access.denied'));
        }
        return $developer;
    }

}