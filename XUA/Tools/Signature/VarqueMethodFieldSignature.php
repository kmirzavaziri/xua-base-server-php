<?php

namespace XUA\Tools\Signature;

use Exception;
use ReflectionClass;
use XUA\Exceptions\InstantiationException;
use XUA\Tools\Entity\EntityFieldSignatureTree;

class VarqueMethodFieldSignature
{
    public EntityFieldSignatureTree $tree;
    public bool $required;
    public mixed $default;
    public bool $const;

    public function __construct() {
        throw new InstantiationException('Use static method fromSignature or fromTree.');
    }

    public static function fromSignature(EntityFieldSignature $signature, $required = true, $default = null, $const = false) {
        $instance = static::instance();
        $instance->tree = new EntityFieldSignatureTree($signature);
        $instance->required = $required;
        $instance->default = $default;
        $instance->const = $const;
        return $instance;
    }

    public static function fromTree(EntityFieldSignatureTree $tree, $required = true, $default = null, $const = false) {
        $instance = static::instance();
        $instance->tree = $tree;
        $instance->required = $required;
        $instance->default = $default;
        $instance->const = $const;
        return $instance;
    }

    public static function fromList(array $list, $required = true, $default = null, $const = false) {
        $instances = [];
        foreach ($list as $item) {
            if (is_a($item, EntityFieldSignature::class)) {
                $instances[] = static::fromSignature($item);
            } elseif (is_a($item, EntityFieldSignatureTree::class)) {
                $instances[] = static::fromTree($item);
            } else {
                throw new Exception('each item must be an instance of either EntityFieldSignature or EntityFieldSignatureTree');
            }
        }
        return $instances;
    }

    private static function instance() : static {
        return (new ReflectionClass(VarqueMethodFieldSignature::class))->newInstanceWithoutConstructor();
    }
}