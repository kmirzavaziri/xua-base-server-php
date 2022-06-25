<?php

namespace Xua\Core\Eves;

use Xua\Core\Exceptions\MagicCallException;
use Xua\Core\Exceptions\MethodRequestException;
use Xua\Core\Exceptions\MethodResponseException;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\URPIService;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Tools\Signature\Signature;

/**
 * Request *************************************************************************************************************
 * ---
 * Response ************************************************************************************************************
 * ---
 */
abstract class MethodEve extends Block
{
    ####################################################################################################################
    # Magics ###########################################################################################################
    ####################################################################################################################
    /**
     * @var MethodRequestException
     */
    public MethodRequestException $_x_error;

    final public function __construct(array $request)
    {
        URPIService::$service::publicMethodInit();

        $this->_x_error = new MethodRequestException();

        $this->_x_values[self::REQUEST_PREFIX] = $request;
        $this->_x_values[self::RESPONSE_PREFIX] = [];

        self::processRequest(static::requestSignatures(), $this->_x_values[self::REQUEST_PREFIX]);
        self::preprocessResponse(static::responseSignatures(), $this->_x_values[self::RESPONSE_PREFIX]);

        $this->validations();
        $this->body();
        $this->logs();

        self::processResponse(static::responseSignatures(), $this->_x_values[self::RESPONSE_PREFIX]);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    ####################################################################################################################
    # Signatures #######################################################################################################
    ####################################################################################################################
    const REQUEST_PREFIX = 'Q_';
    const RESPONSE_PREFIX = '';

    /**
     *
     */
    protected static function registerSignatures(): void
    {
        parent::registerSignatures();
        Signature::registerSignatures(static::class, self::REQUEST_PREFIX, Signature::associate(static::_requestSignatures()));
        Signature::registerSignatures(static::class, self::RESPONSE_PREFIX, Signature::associate(static::_responseSignatures()));
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param Signature $signature
     * @param mixed $value
     */
    final protected function getterProcedure(string $prefix, string $name, Signature $signature, mixed $value): void {}

    /**
     * @param string $prefix
     * @param string $name
     * @param Signature $signature
     * @param mixed $value
     */
    final protected function setterProcedure(string $prefix, string $name, Signature $signature, mixed $value): void
    {
        if (!$signature->declaration->accepts($value, $messages)) {
            throw (new MagicCallException())->setError($name, $messages);
        }
    }

    /**
     * @return Signature[]
     */
    final public static function requestSignatures() : array
    {
        return Signature::signatures(static::class, self::REQUEST_PREFIX);
    }

    /**
     * @return Signature[]
     */
    protected static function _requestSignatures() : array
    {
        return [];
    }

    /**
     * @return Signature[]
     */
    final public static function responseSignatures() : array
    {
        return Signature::signatures(static::class, self::RESPONSE_PREFIX);
    }

    /**
     * @return Signature[]
     */
    protected static function _responseSignatures() : array
    {
        return [];
    }

    ####################################################################################################################
    # Overridable Methods ##############################################################################################
    ####################################################################################################################
    /**
     * @return bool
     */
    static public function isPublic() : bool
    {
        return true;
    }

    /**
     *
     */
    protected function validations() : void {
        // Nothing
    }

    /**
     *
     */
    abstract protected function body() : void;

    /**
     *
     */
    protected function logs() : void {
        // Nothing
    }

    ####################################################################################################################
    # Predefined Methods (Array Casts) #################################################################################
    ####################################################################################################################
    /**
     * @param bool $marshal
     * @return array
     */
    public function toArray(bool $marshal = false): array
    {
        $return = $this->_x_values[self::RESPONSE_PREFIX];
        if ($marshal) {
            $return = (new StructuredMap([
                StructuredMap::structure => array_map(
                    function (Signature $signature) { return $signature->declaration; },
                    static::responseSignatures()
                )
            ]))->nestedMarshal($return);
        }
        return $return;
    }

    ####################################################################################################################
    # Predefined Methods (Errors) ######################################################################################
    ####################################################################################################################
    /**
     * @param string $key
     * @param string|array|null $message
     */
    public function addError(string $key, null|string|array $message): void
    {
        $path = explode('.', $key);
        if ($signature = Signature::_($path[0])) {
            $path[0] = $signature->name;
        }
        $this->_x_error->setError(implode('.', $path), $message);
    }

    public function throwError(): void
    {
        throw $this->_x_error;
    }

    /**
     * @param string $key
     * @param string|array|null $message
     */
    public function addAndThrowError(string $key, null|string|array $message): void
    {
        $this->addError($key, $message);
        $this->throwError();
    }

    ####################################################################################################################
    # Predefined Methods (Signature Value Processors) ##################################################################
    ####################################################################################################################
    /**
     * @param Signature[] $signatures
     * @param array $request
     */
    private static function processRequest(array $signatures, array &$request) {
        $exception = new MethodRequestException();

        $unknownKeys = array_diff(array_keys($request), array_keys($signatures));
        foreach ($unknownKeys as $unknownKey) {
            $exception->setError($unknownKey, ExpressionService::getXua('eves.method_eve.error_message.unknown_request_item'));
        }
        $newRequest = [];
        foreach ($signatures as $key => $signature) {
            if (in_array($key, array_keys($request))) {
                if ($signature->const) {
                    $exception->setError($key, ExpressionService::getXua('eves.method_eve.error_message.cannot_set_constant_request_item'));
                    continue;
                }
            } else {
                if ($signature->required) {
                    $exception->setError($key, ExpressionService::getXua('generic.error_message.required_field_not_provided'));
                    continue;
                } else {
                    $request[$key] = $signature->default;
                }
            }

            if (!$signature->declaration->accepts($request[$key], $messages, [Super::METHOD_UNMARSHAL])) {
                $exception->setError($key, $messages[Super::METHOD_UNMARSHAL]);
            }

            $newRequest[$key] = $request[$key];
        }

        if ($exception->getErrors()) {
            throw $exception;
        }

        $request = $newRequest;
    }

    /**
     * @param Signature[] $signatures
     * @param array $response
     */
    private static function preprocessResponse(array $signatures, array &$response)
    {
        foreach ($signatures as $key => $signature) {
            if (!$signature->required) {
                $response[$key] = $signature->default;
            }
        }
    }

    /**
     * @param Signature[] $signatures
     * @param array $response
     */
    private static function processResponse(array $signatures, array &$response)
    {
        $exception = new MethodResponseException();

        $unknownKeys = array_diff(array_keys($response), array_keys($signatures));
        foreach ($unknownKeys as $unknownKey) {
            $exception->setError($unknownKey, 'Unknown response item');
        }
        $newResponse = [];
        foreach ($signatures as $key => $signature) {
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

            if (!$signature->declaration->accepts($response[$key], $messages)) {
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