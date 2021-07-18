<?php

namespace Services;

use Entities\User;
use Entities\User\Session;
use Services\XUA\ConstantService;
use Services\XUA\DateTimeInstance;
use Services\XUA\SecurityService;
use Supers\Basics\Numerics\Integer;
use Supers\Customs\Email;
use Supers\Customs\IranPhone;
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

        if (!isset($_SERVER['HTTP_USER_ACCESS_TOKEN'])) {
            return;
        }
        $fullAccessToken = $_SERVER['HTTP_USER_ACCESS_TOKEN'];

        $pos = strpos($fullAccessToken, ':');
        if (! $pos or $pos == strlen($fullAccessToken)) {
            return;
        }

        $userId = substr($fullAccessToken, 0, $pos);
        $accessToken = substr($fullAccessToken, $pos + 1, strlen($fullAccessToken) - ($pos + 1));

        if (!(new Integer(['unsigned' => true]))->accepts($userId)) {
            return;
        }

        if (!$accessToken) {
            return;
        }

        self::$session = Session::getOne(
            Condition::leaf(Session::C_user()->rel(User::C_id()), Condition::EQ, $userId)
                ->and(Session::C_accessToken(), Condition::EQ, $accessToken)
        );
        if (!self::$session->id) {
            return;
        }

        self::$session->systemInfo = UserService::getSystemInfo();
        self::$session->ip = $_SERVER['REMOTE_ADDR'];
        self::$session->lastOnline = new DateTimeInstance();
        self::$session->store();

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
        return rand(
            pow(10, ConstantService::VERIFICATION_CODE_LENGTH - 1),
            pow(10, ConstantService::VERIFICATION_CODE_LENGTH) - 1
        );
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

    public static function getUserByEmailOrPhone(string &$emailOrPhone, ?bool &$isEmail): User
    {
        $cellphoneType = new IranPhone(['type' => 'cellphone']);
        $EmailType = new Email([]);
        $isEmail = false;
        $condition = Condition::falseLeaf();
        if ($cellphoneType->accepts($emailOrPhone)) {
            $condition = Condition::leaf(User::C_cellphoneNumber(), Condition::EQ, $emailOrPhone);
        } elseif ($EmailType->accepts($emailOrPhone)) {
            $condition = Condition::leaf(User::C_email(), Condition::EQ, $emailOrPhone);
            $isEmail = true;
        }
        return User::getOne($condition);
    }

    public static function generateAccessToken(Session $session): string
    {
        return password_hash($session->id . '|' . SecurityService::getRandomSalt(32), PASSWORD_DEFAULT);
    }
}