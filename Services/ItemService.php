<?php

namespace Services;

use Entities\Item;
use XUA\Service;

abstract class ItemService extends Service
{
    const LENGTH = 20;
    const REORDER = [14, 17, 1, 12, 8, 3, 9, 13, 0, 2, 7, 5, 19, 10, 4, 16, 11, 6, 18];
    const DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    public static function generateCode(Item $item) : string
    {
        if ($item->id == null) {
            throw new \Exception('Item must be stored before code generation.');
        }
        $raw = str_pad(strtoupper(base_convert($item->id, 10, 36)), 7, '0', STR_PAD_LEFT) .
            self::random(self::LENGTH - 7);
        $result = '';
        foreach (self::REORDER as $offset) {
            $result .= substr($raw, $offset, 1);
        }
        $result .= self::controlBit($result);
        return $result;
    }

    private static function random(int $length): string
    {
        $result = '';
        $count = count(self::DIGITS);
        for ($i = 0; $i < $length; $i++) {
            $result .= self::DIGITS[random_int(0, $count - 1)];
        }
        return $result;
    }

    private static function controlBit(string $code): string
    {
        $sum = 0;
        for ($i = 0; $i < self::LENGTH - 1; $i++) {
            $sum += ((self::LENGTH - $i) * base_convert($code[$i], 36, 10)) % 36;
        }
        $sum = $sum % 36;
        return strtoupper(base_convert($sum, 10 ,36));
    }

}