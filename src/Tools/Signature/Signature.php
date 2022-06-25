<?php

namespace Xua\Core\Tools\Signature;

use Xua\Core\Eves\Super;
use Xua\Core\Exceptions\MagicCallException;

/**
 * @property string fullName
 * @property string halfName
 */
final class Signature
{
    ####################################################################################################################
    # Getter & Setter ##################################################################################################
    ####################################################################################################################
    public function __get(string $name)
    {
        return match ($name) {
            'fullName' => $this->class . '::' . $this->prefix . $this->name,
            'halfName' => $this->prefix . $this->name,
            default => throw new MagicCallException(),
        };
    }

    ####################################################################################################################
    # Signatures Holder ################################################################################################
    ####################################################################################################################
    /**
     * @var Signature[][][]
     */
    private static array $_x_signatures = [];

    public static function _(string $classOrFullName, ?string $prefixOrHalfName = null, ?string $name = null): ?Signature
    {
        if ($prefixOrHalfName === null) {
            [$class, $prefix, $name] = self::explodeSignatureName($classOrFullName);
            @class_exists($class, true);
            return @self::$_x_signatures[$class][$prefix][$name];
        }

        @class_exists($classOrFullName, true);

        if ($name === null) {
            [$prefix, $name] = self::explodeSignatureHalfName($prefixOrHalfName);
            return @self::$_x_signatures[$classOrFullName][$prefix][$name];
        }
        return @self::$_x_signatures[$classOrFullName][$prefixOrHalfName][$name];
    }

    /**
     * @param string $class
     * @param string|null $prefix
     * @return array|Signature
     */
    public static function signatures(string $class, ?string $prefix = null): array|Signature
    {
        @class_exists($class, true);

        if ($prefix === null) {
            return array_merge(...(isset(self::$_x_signatures[$class]) ? array_values(self::$_x_signatures[$class]) : []));
        }
        return (self::$_x_signatures[$class][$prefix]) ?? [];
    }

    /**
     * @param string $class
     * @param string $prefix
     * @param array $signatures
     */
    public static function registerSignatures(string $class, string $prefix, array $signatures): void
    {
        self::$_x_signatures[$class][$prefix] = $signatures;
    }

    ####################################################################################################################
    # Signature Properties #############################################################################################
    ####################################################################################################################
    /**
     * @var string
     */
    public string $class;

    /**
     * @var string
     */
    public string $prefix;

    /**
     * @var string
     */
    public string $name;

    /**
     * @param bool|null $const
     * @param string|null $fullName
     * @param bool|null $required
     * @param mixed $default
     * @param \Xua\Core\Eves\Super $declaration
     */
    private function __construct(
        public ?bool $const,
        ?string      $fullName,
        public ?bool $required,
        public mixed $default,
        public Super $declaration,
    ) {
        if ($fullName !== null) {
            $this->setFullName($fullName);
        }
    }

    /**
     * @param bool|null $const
     * @param string|null $fullName
     * @param bool|null $required
     * @param mixed $default
     * @param Super $declaration
     * @return Signature
     */
    public static function new(
        ?bool   $const,
        ?string $fullName,
        ?bool   $required,
        mixed   $default,
        Super   $declaration,
    ): Signature
    {
        return new self($const, $fullName, $required, $default, $declaration);
    }

    ####################################################################################################################
    # Parametric Signatures ############################################################################################
    ####################################################################################################################
    /**
     * @var array
     */
    private array $params = [];

    /**
     * @param array|null $params
     * @return array|$this
     */
    public function p(?array $params = null) : array|self
    {
        if ($params === null) {
            return $this->params;
        } else {
            $this->params = $params;
            return $this;
        }
    }

    ####################################################################################################################
    # Static Helpers ###################################################################################################
    ####################################################################################################################
    /**
     * @param string $fullName
     * @return $this
     */
    public function setFullName(string $fullName): self
    {
        [$class, $prefix, $name] = self::explodeSignatureName($fullName);
        $this->class = $class;
        $this->prefix = $prefix;
        $this->name = $name;
        return $this;
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

    /**
     * @param string $fullName
     * @return array
     */
    public static function explodeSignatureName(string $fullName): array
    {
        if (str_contains($fullName, '::')) {
            [$class, $halfName] = explode('::', $fullName, 2);
        } else {
            [$class, $halfName] = [null, $fullName];
        }
        [$prefix, $name] = self::explodeSignatureHalfName($halfName);
        return [$class, $prefix, $name];
    }

    /**
     * @param string $halfName
     * @return string[]
     */
    public static function explodeSignatureHalfName(string $halfName): array
    {
        if (str_contains($halfName, '_')) {
            [$prefix, $name] = explode('_', $halfName, 2);
            return [$prefix . '_', $name];
        } else {
            return ['', $halfName];
        }
    }
}