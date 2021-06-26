<?php

namespace Services;

use XUA\Service;

abstract class EmailService extends Service
{
    public static function send(string $emailAddress, string $title, string $content) : void
    {
        // @TODO implement
    }
}