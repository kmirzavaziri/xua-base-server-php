<?php

namespace Services;

use XUA\Service;

abstract class SmsService extends Service
{
    public static function send(string $cellPhoneNumber, string $content) : void
    {
        // @TODO implement
    }
}