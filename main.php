<?php
date_default_timezone_set('Asia/Tehran');

require 'magic.php';

use Services\MainService;
use Services\XUA\Dev\Credentials;
use Services\XUA\RouteService;

require 'autoload.php';

if (Credentials::developer()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

try {
    MainService::before();
    $response = RouteService::getInterface($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'])::execute();
    MainService::after();
    echo $response;
} catch (Throwable $throwable) {
    MainService::catch($throwable);
}