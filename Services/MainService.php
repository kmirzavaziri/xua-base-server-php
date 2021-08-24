<?php

namespace Services;

use Interfaces\XUA\NotFoundInterface;
use Services\XUA\Dev\Credentials;
use Services\XUA\TemplateService;
use Throwable;
use XUA\Entity;
use XUA\Exceptions\RouteException;
use XUA\Service;
use XUA\XUAException;

class MainService extends Service
{


    public static function before()
    {
        Entity::startTransaction();
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