<?php

namespace Xua\Core\Services;

use Xua\Core\Exceptions\DieException;
use Xua\Core\Interfaces\NotFoundInterface;
use Xua\Core\Services\Dev\Credentials;
use Throwable;
use Xua\Core\Eves\Entity;
use Xua\Core\Exceptions\RouteException;
use Xua\Core\Eves\Service;
use Xua\Core\Eves\XuaException;

class MainService extends Service
{
    public static function before()
    {
        session_start();
        header_remove('X-Powered-By');
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
            static::before();
            RouteService::execute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
            static::after();
        } catch (DieException) {
            static::after();
        } catch (Throwable $throwable) {
            try {
                static::afterException();
            } catch (Throwable $t) {
                static::catch($t);
            }
            static::catch($throwable);
        }
    }

    public static function after()
    {
        Entity::commit();
    }

    public static function afterException()
    {
        Entity::rollback();
    }

    public static function catch(Throwable $throwable)
    {
        try {
            if ($throwable instanceof RouteException) {
                static::notFound();
            } elseif (Credentials::developer()) {
                echo
                    "<pre>" . get_class($throwable) . " occurred on " . $throwable->getFile() . ":" . $throwable->getLine() . ":\n\n" .
                    (is_a($throwable, XuaException::class) ? $throwable->displayErrors() : $throwable->getMessage()) . "\n\n" .
                    "Trace:\n" .
                    $throwable->getTraceAsString() .
                    "</pre>";
            } else {
                self::serverError();
            }
        } catch (Throwable) {
            http_response_code(500);
            echo '<h2>500 Internal Server Error</h2><br />';
        } finally {
            exit();
        }
    }

    protected static function serverError(): void
    {
        NotFoundInterface::execute();
    }

    protected static function notFound(): void
    {
        NotFoundInterface::execute();
    }
}