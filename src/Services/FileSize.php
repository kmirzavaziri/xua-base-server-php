<?php

namespace XUA\Services;

use XUA\Eves\Service;

abstract class FileSize extends Service
{
    const B = 1;
    const KB = 1024;
    const MB = 1024 * self::KB;
    const GB = 1024 * self::MB;

    public static function decorate(int $size): string
    {
        if ($size >= self::GB) {
            return number_format($size / self::GB, 2) . 'GB';
        }
        if ($size >= self::MB) {
            return number_format($size / self::MB, 2) . 'MB';
        }
        if ($size >= self::KB) {
            return number_format($size / self::KB, 2) . 'KB';
        }
        return $size . 'B';
    }
}