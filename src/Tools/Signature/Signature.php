<?php

namespace Xua\Core\Tools\Signature;

use Xua\Core\Eves\Super;

class Signature
{
    private array $params = [];

    public string $class;
    public string $name;
    public Super $type;
    public array $order;

    public function __construct(
        public ?bool $const,
        public ?string $signatureName,
        public ?bool $required,
        public mixed $default,
        null|Super|array $declaration,
    ) {
        [$this->class, $this->name] = explode('::', $this->signatureName, 2);
        if (is_a($declaration, Super::class)) {
            $this->type = $declaration;
        } else {
            $this->order = $declaration;
        }

    }

    /**
     * @param Signature[] $signatures
     * @return Signature[]
     */
    public static function associate(array $signatures): array
    {
        $result = [];
        foreach ($signatures as $signature) {
            $result[$signature->name] = $signature;
        }
        return $result;
    }

    public static function _(string $signatureName): Signature
    {
        [$class, $name] = explode('::', $signatureName, 2);
        /** @noinspection PhpUndefinedMethodInspection */
        return $class::signature($name);
    }

    public function p(?array $params = null) : array|static
    {
        if ($params === null) {
            return $this->params;
        } else {
            $this->params = $params;
            return $this;
        }
    }
}