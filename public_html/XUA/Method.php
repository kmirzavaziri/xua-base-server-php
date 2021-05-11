<?php

namespace XUA;


use XUA\Exceptions\MethodRequestException;
use XUA\Exceptions\MethodResponseException;
use XUA\Tools\MethodItemSignature;

abstract class Method extends XUA
{
    # Magics
    private static array $_x_request_structure = [];
    private static array $_x_response_structure = [];
    private array $_x_response = [];

    protected static function _init()
    {
        self::$_x_request_structure[static::class] = static::request();
        self::$_x_response_structure[static::class] = static::response();
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

    /**
     * @throws MethodResponseException
     */
    final function __set($key, $value) : void
    {
        throw (new MethodResponseException())->setError($key, 'Cannot set method response.');
    }

    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    # Overridable Methods
    static public function isPublic() : bool
    {
        return true;
    }

    static protected function request() : array
    {
        return [];
    }

    static protected function response() : array
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