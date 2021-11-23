<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

abstract class EnvironmentService extends Service
{
    const ENV_LOCAL = 'local';
    const ENV_DEMO = 'demo';
    const ENV_PROD = 'prod';

    public static function env(): string
    {
        return getenv('ENV_NAME');
    }

    public static function debugMode(): bool
    {
        return self::env() != self::ENV_PROD;
    }

}