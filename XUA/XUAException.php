<?php

namespace XUA;

use Exception;

abstract class XUAException extends Exception
{
    private array $errors = [];
    public function getErrors() : array
    {
        return $this->errors ?: ($this->getMessage() ? ['' => $this->getMessage()] : []);
    }

    public function setError(string $key, mixed $message) : XUAException
    {
        $this->errors[$key] = $message;
        return $this;
    }

    public function unsetError(string $key) : XUAException
    {
        unset($this->errors[$key]);
        return $this;
    }
}