<?php

namespace Services;

use Services\Exceptions\CurlException;
use Supers\Basics\Highers\Map;
use XUA\Service;

abstract class CurlService extends Service
{
    public static function json(string $url, array $data = [], array $header = []): array
    {
        $header[] = 'Content-Type: application/json';
        $header[] = 'Accept: application/json';
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
            throw new CurlException("not valid json: $response");
        }
    }

}