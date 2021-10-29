<?php

namespace Xua\Core\Tools\Signature;

use Exception;
use Xua\Core\Tools\Entity\SignatureTree;

class VarqueMethodFieldSignature
{
    public SignatureTree $root;
    public bool $required;
    public mixed $default;
    public bool $const;

    private function __construct() {}

    public static function fromSignature(string $signatureName, $required = true, $default = null, $const = false): self
    {
        $instance = new static();
        $instance->root = new SignatureTree($signatureName);
        $instance->required = $required;
        $instance->default = $default;
        $instance->const = $const;
        return $instance;
    }

    public static function fromTree(SignatureTree $tree, $required = true, $default = null, $const = false): self
    {
        $instance = new static();
        $instance->root = $tree;
        $instance->required = $required;
        $instance->default = $default;
        $instance->const = $const;
        return $instance;
    }

    /**
     * @param \Xua\Core\Tools\Signature\VarqueMethodFieldSignature[] $list
     * @param bool $required
     * @param null $default
     * @param false $const
     * @return array
     * @throws \Exception
     */
    public static function fromList(array $list, $required = true, $default = null, $const = false): array
    {
        $instances = [];
        foreach ($list as $item) {
            if (is_string($item)) {
                $instances[] = static::fromSignature($item, $required, $default, $const);
            } elseif (is_a($item, SignatureTree::class)) {
                $instances[] = static::fromTree($item, $required, $default, $const);
            } else {
                throw new Exception('each item must be an instance of either Signature or SignatureTree');
            }
        }
        return $instances;
    }
}