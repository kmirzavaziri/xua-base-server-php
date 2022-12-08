<?php

namespace Xua\Core\Eves;

use Exception;
use Xua\Core\Services\Recursion;

abstract class XuaException extends Exception
{
    private array $errors = [];

    public function getErrors() : array
    {
        return $this->errors ?: ($this->getMessage() ? ['' => $this->getMessage()] : []);
    }

    public function displayErrors() : string
    {
        if (!$this->errors) {
            return $this->getMessage() ?: '';
        }
        return self::displayErrorsRecursive($this->errors);
    }

    public static function displayErrorsRecursive(array $errors, int $indent = 0): string
    {
        $result = '';
        foreach ($errors as $key => $message) {
            $result .= str_repeat('    ', $indent);
            if (is_array($message)) {
                $result .= "$key:" . PHP_EOL . self::displayErrorsRecursive($message, $indent + 1);
            } else {
                $result .= "$key: " . $message . PHP_EOL;
            }
        }
        return $result;
    }

    public function setError(string $key, null|string|array $message) : static
    {
        Recursion::set($this->errors, explode('.', $key), $message);
        return $this;
    }

    public function unsetError(string $key) : static
    {
        unset($this->errors[$key]);
        return $this;
    }

    public function fromException(self $exception): static
    {
        return self::fromErrors($exception->getErrors());
    }

    public function fromErrors(null|string|array $errors): static
    {
        if (is_string($errors)) {
            $this->setError('', $errors);
        } elseif (is_array($errors)) {
            foreach ($errors as $key => $message) {
                $this->setError($key, $message);
            }
        }
        return $this;
    }

    public function flatten(): array {
        $return = [];
        array_walk_recursive($this->errors, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    public function throwIfHasErrors()
    {
        if ($this->errors) {
            throw $this;
        }
    }
}