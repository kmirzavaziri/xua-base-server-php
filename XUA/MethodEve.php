<?php

namespace XUA;

use ReflectionClass;
use XUA\Exceptions\MagicCallException;
use XUA\Exceptions\MethodRequestException;
use XUA\Exceptions\MethodResponseException;
use XUA\Tools\Signature\MethodItemSignature;

abstract class MethodEve extends XUA
{
    protected MethodRequestException $error;

    # Magics
    /**
     * @var MethodItemSignature[][]
     */
    private static array $_x_request_signatures = [];
    /**
     * @var MethodItemSignature[][]
     */
    private static array $_x_response_signatures = [];
    private array $_x_response = [];
    private array $_x_request;

    protected static function _init(): void
    {
        if (!(new ReflectionClass(static::class))->isAbstract()) {
            self::$_x_request_signatures[static::class] = static::requestSignaturesCalculator();
            self::$_x_response_signatures[static::class] = static::responseSignaturesCalculator();
        }
    }

    /**
     * @throws MethodRequestException
     * @throws MethodResponseException
     */
    final public function __construct(array $request)
    {
        $this->error = new MethodRequestException();
        $this->_x_request = $request;
        MethodItemSignature::processRequest(static::requestSignatures(), $this->_x_request);
        MethodItemSignature::preprocessResponse(static::responseSignatures(), $this->_x_response);
        $this->validations();
        $this->body();
        $this->logs();
        MethodItemSignature::processResponse(static::responseSignatures(), $this->_x_response);
    }

    /**
     * @throws MagicCallException
     */
    final function __get(string $key)
    {
        if (str_starts_with($key, 'Q_')) {
            $key = substr($key, 2, strlen($key) - 2);
            if (!isset(static::requestSignatures()[$key])) {
                throw (new MagicCallException())->setError($key, 'Unknown request item');
            }
            return $this->_x_request[$key];
        } else {
            if (! isset(static::responseSignatures()[$key])) {
                throw (new MagicCallException())->setError($key, 'Unknown response item.');
            }
            return $this->_x_response[$key];
        }
    }

    /**
     * @throws MagicCallException
     */
    final function __set($key, $value) : void
    {
        if (!isset(static::responseSignatures()[$key])) {
            throw (new MagicCallException())->setError($key, 'Unknown response item');
        }
        $signature = static::responseSignatures()[$key];
        if (!$signature->type->accepts($value, $messages)) {
            throw (new MagicCallException())->setError($key, $messages);
        }
        $this->_x_response[$key] = $value;
    }

    /**
     * @throws MagicCallException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (str_starts_with($name, 'Q_')) {
            $key = substr($name, 2, strlen($name) - 2);
            if (!isset(static::requestSignatures()[$key])) {
                throw (new MagicCallException())->setError($key, 'Unknown request item signature');
            }
            $result = static::requestSignatures()[$key];
        } elseif (str_starts_with($name, 'R_')) {
            $key = substr($name, 2, strlen($name) - 2);
            if (!isset(static::responseSignatures()[$key])) {
                throw (new MagicCallException())->setError($key, 'Unknown response item signature');
            }
            $result = static::responseSignatures()[$key];
        } else {
            throw (new MagicCallException("Method $name does not exist."));
        }
        if ($arguments) {
            throw (new MagicCallException())->setError($key, 'A request/response item signature method does not accept arguments');
        }

        return $result;
    }


    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    final public static function requestSignatures() : array
    {
        return self::$_x_request_signatures[static::class];
    }

    final public static function responseSignatures() : array
    {
        return self::$_x_response_signatures[static::class];
    }

    # Overridable Methods
    static public function isPublic() : bool
    {
        return true;
    }

    static protected function _requestSignatures() : array
    {
        return [];
    }

    static protected function _responseSignatures() : array
    {
        return [];
    }

    protected function validations() : void {
        // Nothing
    }

    abstract protected function body() : void;

    protected function logs() : void {
        // Nothing
    }

    # Overridable Method Wrappers
    static protected function requestSignaturesCalculator() : array
    {
        return static::_requestSignatures();
    }

    static protected function responseSignaturesCalculator() : array
    {
        return static::_responseSignatures();
    }

    # Predefined Methods
    public function toArray(): array
    {
        return $this->_x_response;
    }

    protected function addError(string $key, mixed $message): void
    {
        $this->error->setError($key, $message);
    }

    protected function throwError(): void
    {
        throw $this->error;
    }

    protected function addAndThrowError(string $key, mixed $message): void
    {
        throw $this->error->setError($key, $message);
    }
}