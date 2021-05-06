<?php

namespace XUA;

use XUA\Exceptions\SuperArgumentException;
use XUA\Exceptions\SuperMarshalException;
use XUA\Exceptions\SuperValidationException;
use XUA\Tools\SuperArgumentSignature;

abstract class Super extends XUA
{
    # Magics
    private static array $_x_formal;
    private array $_x_actual;

    protected static function _init()
    {
        self::$_x_formal[static::class] = static::arguments();
    }

    final public function __construct(array $args)
    {
        try {
            SuperArgumentSignature::processArguments(static::formal(), $args);
        } catch (SuperArgumentException $e) {
            throw (new SuperValidationException(static::class . ": " . $e->getMessage(), $e->getCode(), $e));
        }
        $this->_x_actual = $args;
        $this->validation();
    }

    final function __get($key)
    {
        if (! isset(static::formal()[$key])) {
            throw new SuperArgumentException("$key is not in (" . implode(', ', array_keys(static::formal())) . ")");
        }
        return $this->actual()[$key];
    }

    final function __set($key, $value) : void
    {
        if (!isset(static::formal()[$key])) {
            throw new SuperArgumentException("$key is not in (" . implode(', ', static::formal()) . ")");
        }
        SuperArgumentSignature::processArgument(static::formal()[$key], $value);
        $this->_x_actual[$key] = $value;
    }

    final public function __toString() : string
    {
        $args = [];
        foreach ($this->actual() as $key => $value) {
            $args[] = $key . ' = ' . xua_var_dump($value);
        }
        $args = implode(', ', $args);
        return static::class . "($args)";
    }

    final public static function formal() : array {
        return self::$_x_formal[static::class];
    }

    final public function actual() : array {
        return $this->_x_actual;
    }

    # Overridable Methods
    protected static function _arguments() : array
    {
        return [];
    }

    protected function _validation() : void
    {
        # Empty by default
    }

    abstract protected function _predicate($input, string &$message = null) : bool;

    protected function _marshal($input)
    {
        return $input;
    }

    protected function _unmarshal($input)
    {
        return $input;
    }

    protected function _marshalDatabase($input)
    {
        return $input;
    }

    protected function _unmarshalDatabase($input)
    {
        return $input;
    }

    protected function _databaseType() : ?string
    {
        return null;
    }

    protected function _phpType() : string
    {
        return 'mixed';
    }

    # Overridable Method Wrappers
    private static function arguments() : array
    {
        return static::_arguments();
    }

    private function validation() : void
    {
        $this->_validation();
    }

    private function predicate($input, string &$message = null) : bool {
        return $this->_predicate($input, $message);
    }

    final public function marshal($input)
    {
        if (!$this->explicitlyAccepts($input, $message)) {
            throw new SuperMarshalException($message);
        }
        return $this->_marshal($input);
    }

    final public function unmarshal($input)
    {
        return $this->_unmarshal($input);
    }

    final public function marshalDatabase($input)
    {
        if (!$this->explicitlyAccepts($input, $message)) {
            throw new SuperMarshalException(static::class . ': ' . $message);
        }
        return $this->_marshalDatabase($input);
    }

    final public function unmarshalDatabase($input)
    {
        return $this->_unmarshalDatabase($input);
    }

    final public function databaseType() : ?string
    {
        return $this->_databaseType();
    }

    final public function phpType() : string
    {
        return $this->_phpType();
    }

    # Predefined Methods
    final public function explicitlyAccepts($input, string &$message = null) : bool
    {
        $result = $this->predicate($input, $message);

        if ($result) {
            $message = null;
        }

        return $result;
    }

    final public function implicitlyAccepts($input, array &$messages = null, array $tryMethods = ['unmarshal', 'unmarshalDatabase']) : bool
    {
        # This way we do not change the real value of $input
        return $this->accepts($input, $messages, $tryMethods);
    }

    final public function accepts(&$input, array &$messages = null, array $tryMethods = ['unmarshal', 'unmarshalDatabase']) : bool
    {
        $messages = ['identity' => null];
        foreach ($tryMethods as $tryMethod) {
            $messages[$tryMethod] = null;
        }

        if ($this->predicate($input, $message)) {
            return true;
        } else {
            $messages['identity'] = $message;
        }

        foreach ($tryMethods as $tryMethod) {
            $tmp = $this->$tryMethod($input);
            if ($this->predicate($tmp, $message)) {
                $input = $tmp;
                return true;
            } else {
                $messages[$tryMethod] = $message;
            }
        }

        return false;
    }
}