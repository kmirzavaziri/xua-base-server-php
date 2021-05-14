<?php

namespace XUA;


use XUA\Exceptions\MethodRequestException;
use XUA\Exceptions\MethodResponseException;
use XUA\Tools\Signature\MethodItemSignature;

abstract class Method extends XUA
{
    # Magics
    private static array $_x_request_structure = [];
    private static array $_x_response_structure = [];
    private array $_x_response = [];

    protected static function _init()
    {
        self::$_x_request_structure[static::class] = static::_request();
        self::$_x_response_structure[static::class] = static::_response();
    }

    /**
     * @throws MethodRequestException
     * @throws MethodResponseException
     */
    final public function __construct(array $request)
    {
        MethodItemSignature::processRequest(self::$_x_request_structure[static::class], $request);
        MethodItemSignature::preprocessResponse(self::$_x_response_structure[static::class], $this->_x_response);
        $this->execute($request);
        MethodItemSignature::processResponse(self::$_x_response_structure[static::class], $this->_x_response);
    }

    final function __get($key)
    {
        if (! isset(self::$_x_response_structure[static::class][$key])) {
            throw (new MethodResponseException())->setError($key, 'Unknown response item.');
        }
        return $this->_x_response[$key];
    }

    final function __set($key, $value) : void
    {
        if (!isset(self::$_x_response_structure[static::class][$key])) {
            throw (new MethodResponseException())->setError($key, 'Unknown response item');
        }
        /** @var MethodItemSignature $signature */
        $signature = self::$_x_response_structure[static::class][$key];
        if (!$signature->type->accepts($value, $messages)) {
            throw (new MethodRequestException())->setError($key, implode(' ', $messages));
        }
        $this->_x_response[$key] = $value;
    }

    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    final public static function request() : array
    {
        return self::$_x_request_structure[static::class];
    }

    final public static function response() : array
    {
        return self::$_x_response_structure[static::class];
    }

    # Overridable Methods
    static public function isPublic() : bool
    {
        return true;
    }

    static protected function _request() : array
    {
        return [];
    }

    static protected function _response() : array
    {
        return [];
    }

    abstract protected function execute(array $request) : void;

    # Predefined Methods
    public function toArray(): array
    {
        return $this->_x_response;
    }
}