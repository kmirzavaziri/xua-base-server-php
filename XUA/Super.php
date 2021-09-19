<?php

namespace XUA;

use XUA\Exceptions\MagicCallException;
use XUA\Exceptions\SuperArgumentException;
use XUA\Exceptions\SuperMarshalException;
use XUA\Exceptions\SuperValidationException;
use XUA\Tools\Signature\SuperArgumentSignature;

abstract class Super extends XUA
{
    # Constants
    const METHOD_IDENTITY = 'identity';
    const METHOD_UNMARSHAL = 'unmarshal';
    const METHOD_UNMARSHAL_DATABASE = 'unmarshalDatabase';

    # Magics
    /**
     * @var SuperArgumentSignature[][]
     */
    private static array $_x_argument_signatures;
    private array $_x_arguments;

    /**
     * @throws SuperValidationException
     */
    protected static function _init(): void
    {
        self::$_x_argument_signatures[static::class] = static::argumentSignaturesCalculator();
    }

    /**
     * @throws SuperValidationException
     */
    final public function __construct(array $args)
    {
        try {
            SuperArgumentSignature::processArguments(static::argumentSignatures(), $args);
        } catch (SuperArgumentException $e) {
            $exception = new SuperValidationException();
            foreach ($e->getErrors() as $key => $message) {
                $exception->setError($key, $message);
            }
            throw $exception;
        }
        $this->_x_arguments = $args;
        $this->validation();
    }

    /**
     * @throws MagicCallException
     */
    final function __get(string $key)
    {
        if (!isset(static::argumentSignatures()[$key])) {
            throw (new MagicCallException())->setError($key, 'Unknown super argument');
        }
        return $this->_x_arguments[$key];
    }

    /**
     * @throws MagicCallException
     */
    final function __set($key, $value) : void
    {
        if (!isset(static::argumentSignatures()[$key])) {
            throw (new MagicCallException())->setError($key, 'Unknown super argument on ' . static::class);
        }
        $signature = static::argumentSignatures()[$key];
        if (!$signature->type->accepts($value, $messages)) {
            throw (new MagicCallException())->setError($key, $messages);
        }
        $this->_x_arguments[$key] = $value;
    }

    /**
     * @throws MagicCallException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (!str_starts_with($name, 'A_')) {
            throw (new MagicCallException("Method $name does not exist."));
        }

        $key = substr($name, 2, strlen($name) - 2);

        if (!isset(static::argumentSignatures()[$key])) {
            throw (new MagicCallException())->setError($key, 'Unknown super argument signature');
        }

        if ($arguments) {
            throw (new MagicCallException())->setError($key, 'A super argument signature method does not accept arguments');
        }

        return static::argumentSignatures()[$key];
    }

    final public function __toString() : string
    {
        $args = [];
        foreach ($this->_x_arguments as $key => $value) {
            $args[] = $key . ' = ' . xua_var_dump($value);
        }
        $args = implode(', ', $args);
        return static::class . "($args)";
    }

    public function __debugInfo(): array
    {
        return $this->_x_arguments;
    }

    final public static function argumentSignatures() : array {
        return self::$_x_argument_signatures[static::class];
    }

    # Overridable Methods
    /**
     * @throws SuperValidationException
     */
    protected static function _argumentSignatures() : array
    {
        return [];
    }

    protected function _validation(SuperValidationException $exception) : void
    {
        # Empty by default
    }

    abstract protected function _predicate($input, null|string|array &$message = null) : bool;

    protected function _marshal(mixed $input): mixed
    {
        return $input;
    }

    protected function _unmarshal(mixed $input): mixed
    {
        return $input;
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        return $input;
    }

    protected function _unmarshalDatabase(mixed $input): mixed
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
    /**
     * @throws SuperValidationException
     */
    private static function argumentSignaturesCalculator() : array
    {
        return static::_argumentSignatures();
    }

    /**
     * @throws SuperValidationException
     */
    private function validation() : void
    {
        $exception = new SuperValidationException;
        $this->_validation($exception);
        if($exception->getErrors()) {
            throw $exception;
        }
    }

    private function predicate($input, string|array &$message = null) : bool {
        return $this->_predicate($input, $message);
    }

    /**
     * @throws SuperMarshalException
     */
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
            throw new SuperMarshalException($message);
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
    final public function explicitlyAccepts($input, null|string|array &$message = null) : bool
    {
        $result = $this->predicate($input, $message);

        if ($result) {
            $message = null;
        }

        return $result;
    }

    final public function implicitlyAccepts($input, array &$messages = null, array $tryMethods = [self::METHOD_IDENTITY, self::METHOD_UNMARSHAL, self::METHOD_UNMARSHAL_DATABASE]) : bool
    {
        # This way we do not change the real value of $input
        return $this->accepts($input, $messages, $tryMethods);
    }

    final public function accepts(&$input, array &$messages = null, array $tryMethods = [self::METHOD_IDENTITY, self::METHOD_UNMARSHAL, self::METHOD_UNMARSHAL_DATABASE]) : bool
    {
        foreach ($tryMethods as $tryMethod) {
            $messages[$tryMethod] = null;
        }

        foreach ($tryMethods as $tryMethod) {
            $tmp = $tryMethod == self::METHOD_IDENTITY ? $input : $this->$tryMethod($input);
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