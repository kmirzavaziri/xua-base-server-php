<?php


namespace Services;


use Entities\User;
use XUA\Entity;
use XUA\Service;
use function geoip_record_by_name;

class IPLocationService extends Service
{
    const API_URL = 'http://ip-api.com/json/{query}?fields=country,city';
    public static function locationFromIp(Entity $entity) : string
    {
        if ($entity->ip and $response = @json_decode(file_get_contents(str_replace('{query}', $entity->ip, self::API_URL)), true)) {
            return $response['city'] . ', ' . $response['country'];
        }
        return '';
    }
}