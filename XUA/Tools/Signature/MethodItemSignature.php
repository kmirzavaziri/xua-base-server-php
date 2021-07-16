<?php


namespace XUA\Tools\Signature;


use Services\XUA\ExpressionService;
use XUA\Exceptions\MethodRequestException;
use XUA\Exceptions\MethodResponseException;
use XUA\Super;

class MethodItemSignature
{
    public function __construct(
        public Super $type,
        public bool $required,
        public $default = null,
        public bool $const = false,
    ) {}

    /**
     * @throws MethodRequestException
     */
    public static function processRequest(array $signatures, array &$request) {
        $exception = new MethodRequestException();

        $unknownKeys = array_diff(array_keys($request), array_keys($signatures));
        foreach ($unknownKeys as $unknownKey) {
            $exception->setError($unknownKey, ExpressionService::get('errormessage.unknown.request.item'));
        }
        $newRequest = [];
        foreach ($signatures as $key => $signature) {
            /** @var MethodItemSignature $signature */

            if (in_array($key, array_keys($request))) {
                if ($signature->const) {
                    $exception->setError($key, ExpressionService::get('errormessage.cannot.set.constant.request.item'));
                    continue;
                }
            } else {
                if ($signature->required) {
                    $exception->setError($key, ExpressionService::get('errormessage.required.request.item.not.provided'));
                    continue;
                } else {
                    $request[$key] = $signature->default;
                }
            }

            if (!$signature->type->accepts($request[$key], $messages, [Super::METHOD_UNMARSHAL])) {
                $exception->setError($key, $messages[Super::METHOD_UNMARSHAL]);
            }

            $newRequest[$key] = $request[$key];
        }

        if ($exception->getErrors()) {
            throw $exception;
        }

        $request = $newRequest;
    }

    public static function preprocessResponse(array $signatures, array &$response)
    {
        foreach ($signatures as $key => $signature) {
            /** @var MethodItemSignature $signature */
            if (!$signature->required) {
                $response[$key] = $signature->default;
            }
        }
    }

    /**
     * @throws MethodResponseException
     */
    public static function processResponse(array $signatures, array &$response)
    {
        $exception = new MethodResponseException();

        $unknownKeys = array_diff(array_keys($response), array_keys($signatures));
        foreach ($unknownKeys as $unknownKey) {
            $exception->setError($unknownKey, 'Unknown response item');
        }
        $newResponse = [];
        foreach ($signatures as $key => $signature) {
            /** @var MethodItemSignature $signature */

            if (in_array($key, array_keys($response))) {
                if ($signature->const) {
                    $exception->setError($key, 'Cannot set constant response item');
                    continue;
                }
            } else {
                if ($signature->required) {
                    $exception->setError($key, 'Required response item not provided');
                    continue;
                } else {
                    $response[$key] = $signature->default;
                }
            }

            if (!$signature->type->accepts($response[$key], $messages)) {
                $exception->setError($key, $messages);
            }

            $newResponse[$key] = $response[$key];
        }

        if ($exception->getErrors()) {
            throw $exception;
        }

        $response = $newResponse;
    }
}