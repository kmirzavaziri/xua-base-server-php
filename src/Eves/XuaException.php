<?php

namespace Xua\Core\Eves;

use Exception;

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
        $path = explode('.', $key);
        $errors = &$this->errors;
        foreach ($path as $part) {
            if (!isset($errors[$part]) or !is_array($errors[$part])) {
                $errors[$part] = [];
            }
            $errors = &$errors[$part];
        }
        $errors = $message;
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
}