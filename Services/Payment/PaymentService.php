<?php

namespace Services\Payment;

use Entities\Transaction;
use Exception;
use XUA\Service;

abstract class PaymentService extends Service
{
    const SERVICE_ZARINPAL = 'zarinpal';
    const SERVICE_ALL = [
        self::SERVICE_ZARINPAL
    ];

    abstract public static function createNewTransaction(int $amount, array $metaData, string $callbackUrl, ?string &$url = null): Transaction;

    abstract public static function verifyTransaction(string $paymentServiceUid, ?string &$message = null): string;

    public static function getService(string $paymentService): string
    {
        switch ($paymentService) {
            case self::SERVICE_ZARINPAL:
                return ZarinpalService::class;
            default:
                throw new Exception('Unknown payment service ' . $paymentService);
        }
    }
}