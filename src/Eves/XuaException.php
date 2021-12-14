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

    public static function displayErrorsRecursive(array $errors): string
    {
        $result = '';
        foreach ($errors as $key => $message) {
            if (is_array($message)) {
                $result .= "$key: " . self::displayErrorsRecursive($message);
            } else {
                $result .= "$key: " . $message;
            }
        }
        return $result;
    }

    public function setError(string $key, null|string|array $message) : static
    {
        $this->errors[$key] = $message;
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

    public function fromErrors(array $errors): static
    {
        foreach ($errors as $key => $message) {
            $this->setError($key, $message);
        }
        return $this;
    }
}