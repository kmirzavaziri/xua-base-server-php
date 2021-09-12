<?php

namespace Interfaces;

use XUA\InterfaceEve;

class UniversalResourcePoolOptionsInterface extends InterfaceEve
{
    public static function execute(): string
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // @TODO only allow localhost for $_SERVER['HTTP_ORIGIN']
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        return '';
    }
}