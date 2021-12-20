<?php

namespace Xua\Core\Methods;

use Xua\Core\Eves\Method;
use Xua\Core\Eves\MethodEve;
use Xua\Core\Exceptions\MethodRequestException;
use Xua\Core\Services\ConstantService;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\SecurityService;
use Xua\Core\Supers\Highers\Map;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Signature\Signature;

/**
 * Request *************************************************************************************************************
 * @property array Q_methods
 * Response ************************************************************************************************************
 * @property array responses
 * *********************************************************************************************************************
 */
class Buffer extends Method
{
    /* Request ****************************************************************************************************** */
    const Q_methods = self::class . '::Q_methods';
    /* Response ***************************************************************************************************** */
    const responses = self::class . '::responses';
    /* ************************************************************************************************************** */

    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            Signature::new(false, static::Q_methods, true, null, new Map([
                Map::keyType => new Text([]),
                Map::valueType => new Map([]),
                Map::nullable => false,
                Map::minSize => 1,
            ])),
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::responseSignatures(), [
            Signature::new(false, static::responses, true, null, new Map([
                Map::keyType => new Text([]),
                Map::valueType => new Map([]),
                Map::nullable => false,
                Map::minSize => 1,
            ])),
        ]);
    }

    protected function body(): void
    {
        $responses = [];
        foreach ($this->Q_methods as $method => $request) {
            $class = ConstantService::get('config', 'services.urpi.rootNamespace') . "\\" . str_replace('/', "\\", $method);
            if (
                !is_a($class, MethodEve::class, true) or
                (!$class::isPublic() and !SecurityService::verifyPrivateMethodAccess())
            ) {
                $this->addAndThrowError(static::Q_methods, [$method => ExpressionService::getXua('methods.buffer.error_message.method_not_found')]);
            }
            $response = [
                'errors' => [],
                'response' => [],
            ];
            try {
                $response['response'] = (new $class($request))->toArray();
                foreach ($response['response'] as $key => $value) {
                    // @TODO how should we handle this
                    // $response['response'][$key] = Signature::_($class, $key)->declaration->marshal($value);
                    $response['response'][$key] = $value;
                }
            } catch (MethodRequestException $e) {
                $response['errors'] = $e->getErrors();
            }
            $responses[$method] = $response;
        }

        $this->responses = $responses;
    }
}