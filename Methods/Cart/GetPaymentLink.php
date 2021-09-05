<?php

namespace Methods\Cart;

use Services\JsonLogService;
use Services\Payment\PaymentService;
use Services\UserService;
use Services\XUA\ConstantService;
use Services\XUA\ExpressionService;
use Supers\Customs\Url;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property string link
 * @method static MethodItemSignature R_link() The Signature of: Response Item `link`
 */
class GetPaymentLink extends Method
{
    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            'link' => new MethodItemSignature(new Url(['nullable' => false, 'schemes' => ['https://']]), true, null, false)
        ]);
    }

    protected function body(): void
    {
        $serviceName = PaymentService::SERVICE_ZARINPAL;

        /** @var PaymentService $serviceClass */
        $serviceClass = PaymentService::getService($serviceName);
        // @TODO use inverse of route detection and get from interface (for callback)
        $transaction = $serviceClass::createNewTransaction(
            '10000',
            [
                'description' => ExpressionService::get('payment.cart.description'),
                'mobile' => UserService::user()->cellphoneNumber,
                'email' => UserService::user()->email,
            ],
            ConstantService::url() . 'cart/payment-zarinpal',
            $link
        );
        $this->link = $link;
        // @TODO connect cart to $transactionId
    }

    protected function validations(): void
    {
        UserService::verifyUser($this->error);
    }
}