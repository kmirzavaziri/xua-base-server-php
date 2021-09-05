<?php

namespace Services\Payment;

use Entities\Transaction;
use Services\CurlService;
use Services\EnvironmentService;
use Services\Exceptions\PaymentException;
use Services\UserService;
use Services\XUA\ConstantService;
use Services\XUA\DateTimeInstance;

abstract class ZarinpalService extends PaymentService
{
    const ZARINPAL_CREATE_NEW_TRANSACTION_URL = 'https://api.zarinpal.com/pg/v4/payment/request.json';
    const ZARINPAL_CREATE_NEW_TRANSACTION_URL_TEST = 'https://sandbox.zarinpal.com/pg/v4/payment/request.json';
    const ZARINPAL_START_PAY_URL = 'https://www.zarinpal.com/pg/StartPay';
    const ZARINPAL_START_PAY_URL_TEST = 'https://sandbox.zarinpal.com/pg/StartPay';

    public static function createNewTransaction(int $amount, array $metaData, string $callbackUrl, ?string &$url = null): Transaction
    {
        $user = UserService::verifyUser();

        $url = EnvironmentService::getEnv() == EnvironmentService::ENV_PROD
            ? self::ZARINPAL_CREATE_NEW_TRANSACTION_URL
            : self::ZARINPAL_CREATE_NEW_TRANSACTION_URL_TEST;

        $response = CurlService::json($url, [
            'merchant_id' => ConstantService::get('config/payment/zarinpal', 'merchantId'),
            'amount' => $amount,
            'description' => $metaData['description'] ?? '',
            'callback_url' => $callbackUrl,
            'metadata' => [
                'mobile' => $metaData['mobile'] ?? '',
                'email' => $metaData['email'] ?? '',
            ],
        ]);

        if (
            !$response['errors'] and
            isset($response['data']) and
            isset($response['data']['code']) and $response['data']['code'] == 100 and
            isset($response['data']['authority'])
        ) {
            $transaction = new Transaction();
            $transaction->user = $user;
            $transaction->paymentService = PaymentService::SERVICE_ZARINPAL;
            $transaction->paymentServiceUid = $response['data']['authority'];
            $transaction->amount = $amount;
            $transaction->verified = false;
            $transaction->createdAt = new DateTimeInstance();
            $transaction->store();

            $startPayUrl = EnvironmentService::getEnv() == EnvironmentService::ENV_PROD
                ? self::ZARINPAL_START_PAY_URL
                : self::ZARINPAL_START_PAY_URL_TEST;
            $url = $startPayUrl . '/' . $response['data']['authority'];
            return $transaction;
        } else {
            throw new PaymentException('[' . $response['errors']['code'] . '] ' . $response['errors']['message']);
        }
    }

    public static function verifyTransaction(string $paymentServiceUid, ?string &$message = null): string
    {
        // @TODO
        return '';
    }
}