<?php

namespace XUA\Services;

use XUA\Interfaces\NotFoundInterface;
use XUA\Services\Dev\Credentials;
use Throwable;
use XUA\Eves\Entity;
use XUA\Exceptions\RouteException;
use XUA\Eves\Service;
use XUA\Eves\XUAException;

class MainService extends Service
{
    public static function before()
    {
        Entity::startTransaction();
    }

    public static function main()
    {
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
            self::before();
            $response = RouteService::getInterface($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'])::execute();
            self::after();
            echo $response;
        } catch (Throwable $throwable) {
            self::catch($throwable);
        }
    }

    public static function after()
    {
        Entity::commit();
    }

    public static function catch(Throwable $throwable)
    {
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
}