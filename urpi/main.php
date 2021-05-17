<?php

require 'magic.php';

use Interfaces\XUA\NotFoundInterface;
use Services\XUA\Dev\Credentials;
use Services\XUA\RouteService;
use Services\XUA\TemplateService;
use XUA\Exceptions\RouteException;
use XUA\XUAException;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'autoload.php';

try {
    echo RouteService::getInterface($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'])::execute();
} catch (Throwable $throwable) {
    if (Credentials::developer()) {
        echo
            "<pre>" . get_class($throwable) . " occurred on " . $throwable->getFile() . ":" . $throwable->getLine() . ":\n\n" .
            (is_a($throwable, XUAException::class) ? xua_var_dump($throwable->getErrors()) : $throwable->getMessage()) . "\n\n" .
            "Trace:\n" .
            $throwable->getTraceAsString() .
            "</pre>";
    } else {
        if ($throwable instanceof RouteException) {
            echo NotFoundInterface::execute();
        } else {
            TemplateService::render('errors/500.twig', []);
        }
    }
}