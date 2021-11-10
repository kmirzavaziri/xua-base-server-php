<?php

namespace Xua\Core\Eves;

use Xua\Core\Exceptions\MagicCallException;
use Xua\Core\Exceptions\SuperArgumentException;
use Xua\Core\Exceptions\SuperMarshalException;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Tools\Signature\Signature;

abstract class Super extends Block
{
    ####################################################################################################################
    # Constants #########################################################################################################
    ####################################################################################################################
    const METHOD_IDENTITY = 'identity';
    const METHOD_UNMARSHAL = 'unmarshal';
    const METHOD_UNMARSHAL_DATABASE = 'unmarshalDatabase';

    ####################################################################################################################
    # Magics ###########################################################################################################
    ####################################################################################################################
    /**
     *
     */
    final public function __construct(array $args)
    {
        try {
            self::processArguments(static::argumentSignatures(), $args);
        } catch (SuperArgumentException $e) {
            $exception = new SuperValidationException();
            foreach ($e->getErrors() as $key => $message) {
                $exception->setError($key, $message);
            }
            throw $exception;
        }
        $this->_x_values[self::ARGUMENT_PREFIX] = $args;
        $this->validation();
    }

    /**
     * @return string
     */
    final public function __toString() : string
    {
        $args = [];
        foreach ($this->_x_values[self::ARGUMENT_PREFIX] as $key => $value) {
            $args[] = $key . ' = ' . xua_var_dump($value);
        }
        $args = implode(', ', $args);
        return static::class . "($args)";
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->_x_values[self::ARGUMENT_PREFIX];
    }

    ####################################################################################################################
    # Signatures #######################################################################################################
    ####################################################################################################################
    const ARGUMENT_PREFIX = '';

    /**
     *
     */
    protected static function registerSignatures(): void
    {
        parent::registerSignatures();
        Signature::registerSignatures(static::class, self::ARGUMENT_PREFIX, Signature::associate(static::_argumentSignatures()));
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
     * @throws MagicCallException
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
    final public static function argumentSignatures() : array
    {
        return Signature::signatures(static::class, self::ARGUMENT_PREFIX);
    }

    /**
     * @return Signature[]
     */
    protected static function _argumentSignatures() : array
    {
        return [];
    }

    ####################################################################################################################
    # Overridable Methods ##############################################################################################
    ####################################################################################################################
    /**
     * @param SuperValidationException $exception
     */
    protected function _validation(SuperValidationException $exception) : void
    {
        # Empty by default
    }

    /**
     * @param $input
     * @param string|array|null $message
     * @return bool
     */
    abstract protected function _predicate($input, null|string|array &$message = null) : bool;

    /**
     * @param mixed $input
     * @return mixed
     */
    protected function _marshal(mixed $input): mixed
    {
        return $input;
    }

    /**
     * @param mixed $input
     * @return mixed
     */
    protected function _unmarshal(mixed $input): mixed
    {
        return $input;
    }

    /**
     * @param mixed $input
     * @return mixed
     */
    protected function _marshalDatabase(mixed $input): mixed
    {
        return $input;
    }

    /**
     * @param mixed $input
     * @return mixed
     */
    protected function _unmarshalDatabase(mixed $input): mixed
    {
        return $input;
    }

    /**
     * @return string|null
     */
    protected function _databaseType() : ?string
    {
        return null;
    }

    /**
     * @return string
     */
    protected function _phpType() : string
    {
        return 'mixed';
    }

    ####################################################################################################################
    # Overridable Method Wrappers ######################################################################################
    ####################################################################################################################
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

    /**
     * @param $input
     * @param string|array|null $message
     * @return bool
     */
    private function predicate($input, string|array &$message = null) : bool {
        return $this->_predicate($input, $message);
    }

    /**
     * @param $input
     * @return mixed
     * @throws SuperMarshalException
     */
    final public function marshal($input): mixed
    {
        if (!$this->explicitlyAccepts($input, $message)) {
            throw new SuperMarshalException($message);
        }
        return $this->_marshal($input);
    }

    /**
     * @param $input
     * @return mixed
     */
    final public function unmarshal($input): mixed
    {
        return $this->_unmarshal($input);
    }

    /**
     * @param $input
     * @return mixed
     * @throws SuperMarshalException
     */
    final public function marshalDatabase($input): mixed
    {
        if (!$this->explicitlyAccepts($input, $message)) {
            throw new SuperMarshalException($message);
        }
        return $this->_marshalDatabase($input);
    }

    /**
     * @param $input
     * @return mixed
     */
    final public function unmarshalDatabase($input): mixed
    {
        return $this->_unmarshalDatabase($input);
    }

    /**
     * @return string|null
     */
    final public function databaseType() : ?string
    {
        return $this->_databaseType();
    }

    /**
     * @return string
     */
    final public function phpType() : string
    {
        return $this->_phpType();
    }

    ####################################################################################################################
    # Predefined Methods (predicate interpreters) ######################################################################
    ####################################################################################################################
    /**
     * @param $input
     * @param string|array|null $message
     * @return bool
     */
    final public function explicitlyAccepts($input, null|string|array &$message = null) : bool
    {
        $result = $this->predicate($input, $message);

        if ($result) {
            $message = null;
        }

        return $result;
    }

    /**
     * @param $input
     * @param array|null $messages
     * @param array|string[] $tryMethods
     * @return bool
     */
    final public function implicitlyAccepts($input, array &$messages = null, array $tryMethods = [self::METHOD_IDENTITY, self::METHOD_UNMARSHAL, self::METHOD_UNMARSHAL_DATABASE]) : bool
    {
        # This way we do not change the real value of $input
        return $this->accepts($input, $messages, $tryMethods);
    }

    /**
     * @param $input
     * @param array|null $messages
     * @param array|string[] $tryMethods
     * @return bool
     */
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

    ####################################################################################################################
    # Predefined Methods (Signature Value Processors) ##################################################################
    ####################################################################################################################
    /**
     * @param Signature[] $signatures
     * @param array $args
     * @throws SuperArgumentException
     */
    private static function processArguments(array $signatures, array &$args) {
        $exception = new SuperArgumentException();
        foreach ($args as $signatureFullName => $arg) {
            $signature = Signature::_($signatureFullName);
            if ($signature === null or $signature->class != static::class or $signature->prefix != self::ARGUMENT_PREFIX) {
                $exception->setError($signatureFullName, 'Unknown argument signature');
            }
        }
        $newArgs = [];
        foreach ($signatures as $signature) {
            if (in_array($signature->fullName, array_keys($args))) {
                if ($signature->const) {
                    $exception->setError($signature->name, 'Cannot set constant argument');
                    continue;
                }
            } else {
                if ($signature->required) {
                    $exception->setError($signature->name, 'Required argument not provided');
                    continue;
                } else {
                    $args[$signature->fullName] = $signature->default;
                }
            }

            if (!$signature->declaration->accepts($args[$signature->fullName], $messages)) {
                $exception->setError($signature->name, $messages);
            }

            $newArgs[$signature->name] = $args[$signature->fullName];
        }

        if($exception->getErrors()) {
            throw $exception;
        }

        $args = $newArgs;
    }
}