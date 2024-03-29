<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;
use Xua\Core\Exceptions\JsonLogException;

abstract class JsonLogService extends Service
{
    /**
     * @param string $key
     * @return array|null
     */
    public static function getAll(string $key): ?array {
        return @json_decode(@file_get_contents(self::filename($key)), true);
    }

    /**
     * @param string $key
     * @param int $pageSize
     * @param int $pageIndex
     * @return array|null
     */
    public static function getMany(string $key, int $pageSize, int $pageIndex): ?array {
        if ($pageIndex < 1) {
            $pageIndex = 1;
        }
        $offset = $pageSize * ($pageIndex - 1);
        $all = self::getAll($key);
        return $all ? array_slice($all, $offset, $pageSize) : null;
    }

    /**
     * @param string $key
     * @param mixed $data
     * @return void
     */
    public static function append(string $key, mixed $data): void
    {
        $logs = self::getAll($key);
        if (!$logs or !is_array($logs)) {
            $logs = [];
        }
        $logs[] = $data;

        $filename = self::filename($key);
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!file_exists($dir)) {
            throw new JsonLogException('Somehow failed');
        }
        file_put_contents($filename, json_encode($logs));
    }

    /**
     * @param string $key
     * @return void
     */
    public static function removeAll(string $key): void
    {
        $filename = self::filename($key);
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!file_exists($dir)) {
            throw new JsonLogException('Somehow failed');
        }
        file_put_contents($filename, '');
    }

    /**
     * @param string $key
     * @return string
     */
    public static function filename(string $key): string {
        return ConstantService::get('config', 'paths.logs') . DIRECTORY_SEPARATOR . $key . '.json';
    }
}