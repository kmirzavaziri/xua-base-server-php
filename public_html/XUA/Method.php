<?php

namespace XUA;


use XUA\Exceptions\ClassMethodCallException;
use XUA\Exceptions\MethodRequestException;
use XUA\Exceptions\MethodResponseException;
use XUA\Tools\Signature\MethodItemSignature;

abstract class Method extends XUA
{
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

    protected static function _init()
    {
        self::$_x_request_signatures[static::class] = static::requestSignaturesCalculator();
        self::$_x_response_signatures[static::class] = static::responseSignaturesCalculator();
    }

    /**
     * @throws MethodRequestException
     * @throws MethodResponseException
     */
    final public function __construct(array $request)
    {
        $this->_x_request = $request;
        MethodItemSignature::processRequest(static::requestSignatures(), $this->_x_request);
        MethodItemSignature::preprocessResponse(static::responseSignatures(), $this->_x_response);
        $this->execute();
        MethodItemSignature::processResponse(static::responseSignatures(), $this->_x_response);
    }

    /**
     * @throws MethodResponseException
     * @throws MethodRequestException
     */
    final function __get($key)
    {
        if (str_starts_with($key, 'Q_')) {
            $key = substr($key, 2, strlen($key) - 2);
            if (!isset(static::requestSignatures()[$key])) {
                throw (new MethodRequestException())->setError($key, 'Unknown request item');
            }
            return $this->_x_request[$key];
        } else {
            if (! isset(static::responseSignatures()[$key])) {
                throw (new MethodResponseException())->setError($key, 'Unknown response item.');
            }
            return $this->_x_response[$key];
        }
    }

    /**
     * @throws MethodRequestException
     * @throws MethodResponseException
     */
    final function __set($key, $value) : void
    {
        if (!isset(static::responseSignatures()[$key])) {
            throw (new MethodResponseException())->setError($key, 'Unknown response item');
        }
        $signature = static::responseSignatures()[$key];
        if (!$signature->type->accepts($value, $messages)) {
            throw (new MethodRequestException())->setError($key, $messages);
        }
        $this->_x_response[$key] = $value;
    }

    /**
     * @throws ClassMethodCallException
     * @throws MethodResponseException
     * @throws MethodRequestException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (str_starts_with($name, 'Q_')) {
            $key = substr($name, 2, strlen($name) - 2);
            if (!isset(static::requestSignatures()[$key])) {
                throw (new MethodRequestException())->setError($key, 'Unknown request item signature');
            }
            $result = static::requestSignatures()[$key];
        } elseif (str_starts_with($name, 'R_')) {
            $key = substr($name, 2, strlen($name) - 2);
            if (!isset(static::responseSignatures()[$key])) {
                throw (new MethodResponseException())->setError($key, 'Unknown response item signature');
            }
            $result = static::responseSignatures()[$key];
        } else {
            throw (new ClassMethodCallException("Method $name does not exist."));
        }
        if ($arguments) {
            throw (new ClassMethodCallException())->setError($key, 'A request/response item signature method does not accept arguments');
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

    abstract protected function execute() : void;

    # Overridable Method Wrappers
    static private function requestSignaturesCalculator() : array
    {
        return static::_requestSignatures();
    }

    static private function responseSignaturesCalculator() : array
    {
        return static::_responseSignatures();
    }

    # Predefined Methods
    public function toArray(): array
    {
        return $this->_x_response;
    }
}