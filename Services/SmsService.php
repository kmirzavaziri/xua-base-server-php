<?php

namespace Services;

use Services\Exceptions\SmsException;
use Services\XUA\ConstantService;
use Supers\Basics\Highers\Map;
use XUA\Service;

abstract class SmsService extends Service
{
    const SMS_IR_SEND_TEMPLATE_URL = 'http://RestfulSms.com/api/UltraFastSend';
    const SMS_IR_API_TOKEN_URL = 'http://RestfulSms.com/api/Token';
    const SMS_IR_VERIFICATION_TEMPLATE_ID = 35186;

    public static function sendTemplate(string $phoneNumber, int $templateId, array $parameters = []) : void
    {
        if (EnvironmentService::getEnv() == EnvironmentService::ENV_PROD) {
            $stupidParameters = [];
            foreach ($parameters as $key => $value) {
                $stupidParameters[] = [
                    'Parameter' => $key,
                    'ParameterValue' => $value
                ];
            }
            $response = self::curl(self::SMS_IR_SEND_TEMPLATE_URL, [
                'Mobile' => $phoneNumber,
                'TemplateId' => $templateId,
                'ParameterArray' => $stupidParameters,
            ], self::getToken());

            $response['Message'] ?? throw new SmsException('Sending sms failed with response: ' . xua_var_dump($response));
        } else {
            JsonLogService::append('sms', ['phoneNumber' => $phoneNumber, 'templateId' => $templateId, 'parameters' => $parameters]);
        }
    }

    private static function getToken() : string
    {
        $response = self::curl(static::SMS_IR_API_TOKEN_URL, [
            'UserApiKey' => ConstantService::get('config/sms', 'apikey'),
            'SecretKey' => ConstantService::get('config/sms', 'secretkey'),
            'System' => 'php_rest_v_1_2'
        ]);

        return $response['TokenKey'] ?? throw new SmsException('TokenKey not found in ' . xua_var_dump($response));
    }

    private static function curl(string $url, array $data, ?string $token = null): array
    {
        $header = ['Content-Type: application/json'];
        if ($token) {
            $header[] = "x-sms-ir-secure-token: $token";
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        $mapType = new Map([]);
        if ($mapType->accepts($response)) {
            /** @var array $response */
            return $response;
        } else {
            throw new SmsException("not valid json: $response");
        }
    }

}