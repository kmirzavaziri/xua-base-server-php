<?php

namespace Interfaces\Cart\Payment;

use Services\Payment\ZarinpalService;
use Services\XUA\ExpressionService;
use Services\XUA\TemplateService;
use XUA\InterfaceEve;

class ZarinpalInterface extends InterfaceEve
{
    public static function execute(): string
    {
        $message = ExpressionService::get('payment.errormessage.nok');
        $literal = [
            'title' => ExpressionService::get('payment.cart.title'),
            'myfarm_title' => ExpressionService::get('payment.cart.myfarm_title'),
            'back_to_app' => ExpressionService::get('payment.cart.back_to_app'),
        ];
        if (
            isset($_GET['Authority']) and
            isset($_GET['Status']) and
            $_GET['Status'] == 'OK' and
            ZarinpalService::verifyTransaction($_GET['Status'], $message)
        ) {
            // @TODO verify cart
            return TemplateService::render('payment/callback.twig', [
                'literal' => $literal,
                'data' => ['success' => true, 'message' => ExpressionService::get('payment.ok')]
            ]);
        } else {
            return TemplateService::render('payment/callback.twig', [
                'literal' => $literal,
                'data' => ['success' => false, 'message' => $message]
            ]);
        }
    }
}