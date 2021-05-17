<?php

namespace Services;

use Entities\User;
use Entities\User\Session;
use XUA\Service;
use XUA\Tools\Entity\Condition;

abstract class UserService extends Service
{
    private static ?Session $session = null;

    protected static function _init(): void
    {
        self::$session = new Session();

        if (isset($_SERVER['USER_ACCESS_TOKEN'])) {
            $pos = strpos($_SERVER['USER_ACCESS_TOKEN'], ':');
            if ($pos and $pos != strlen($_SERVER['USER_ACCESS_TOKEN'])) {
                $userId = substr($_SERVER['USER_ACCESS_TOKEN'], 0, $pos);
                $accessToken = substr($_SERVER['USER_ACCESS_TOKEN'], $pos + 1, strlen($_SERVER['USER_ACCESS_TOKEN']) - ($pos + 1));
                self::$session = Session::getOne(
                    Condition::leaf(Session::C_user()->rel(User::F_id()), Condition::EQ, $userId)
                        ->and(Condition::leaf(Session::C_accessToken(), Condition::EQ, $accessToken))
                );
            }
        }
    }

    public static function session(): Session
    {
        return self::$session;
    }

    public static function user(): User
    {
        return self::$session->user ?? new User();
    }
}