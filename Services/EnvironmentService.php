<?php

namespace Services;

use XUA\Service;

abstract class EnvironmentService extends Service
{
    const ENV_LOCAL = 'local';
    const ENV_DEMO = 'demo';
    const ENV_PROD = 'production';

    public static function getEnv(): string
    {
        return getenv('ENV_NAME');
    }
}