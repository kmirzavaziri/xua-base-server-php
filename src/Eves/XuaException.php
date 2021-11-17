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
        if ($this->errors) {
            $result = '';
            foreach ($this->errors as $key => $message) {
                $result .= "$key: $message";
            }
        } else {
            $result = $this->getMessage() ?: '';
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
        $errors = $exception->getErrors();
        foreach ($errors as $key => $message) {
            $this->setError($key, $message);
        }
        return $this;
    }
}