<?php

namespace Services;

use Entities\User;
use Entities\User\Session;
use Services\XUA\DateTimeInstance;
use XUA\Service;
use XUA\Tools\Entity\Condition;

abstract class UserService extends Service
{
    const REGEX_OS_MAP = [
        '/windows phone 8/i'    =>  'Windows Phone 8',
        '/windows phone os 7/i' =>  'Windows Phone 7',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'              =>  'Windows 98',
        '/win95/i'              =>  'Windows 95',
        '/win16/i'              =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    ];

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
                if (self::$session->id) {
                    self::$session->systemInfo = UserService::getSystemInfo();
                    self::$session->ip = $_SERVER['REMOTE_ADDR'];
                    self::$session->lastOnline = new DateTimeInstance();
                    self::$session->store();
                }
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

    public static function generateVerificationCode() : int
    {
        return rand(100000, 999999);
    }

    public static function getSystemInfo() : string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $os = 'Unknown OS';
        $device = 'SYSTEM';
        foreach (self::REGEX_OS_MAP as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $os = $value;
                $device = !preg_match('/(windows|mac|linux|ubuntu)/i', $os)
                    ? 'MOBILE'
                    : (preg_match('/phone/i', $os)
                        ? 'MOBILE'
                        : 'SYSTEM'
                    );
                break;
            }
        }

        return $device . ' | ' . $os;
    }

}