<?php


namespace XUA\Tools;


use XUA\Exceptions\SuperArgumentException;
use XUA\Super;

class SuperArgumentSignature
{
    public function __construct(
        public Super $type,
        public bool $required,
        public $default = null,
        public bool $const = false,
    ) {}

    public static function processArguments(array $signatures, array &$args) {
        $unknownKeys = array_diff(array_keys($args), array_keys($signatures));
        if ($unknownKeys) {
            throw new SuperArgumentException("Unknown arguments " . implode(', ', $unknownKeys) . ".");
        }
        $newArgs = [];
        foreach ($signatures as $key => $signature) {
            /** @var SuperArgumentSignature $signature */

            if (in_array($key, array_keys($args))) {
                if ($signature->const) {
                    throw new SuperArgumentException("Cannot set constant argument $key.");
                }
            } else {
                if ($signature->required) {
                    throw new SuperArgumentException("Required argument $key not provided.");
                } else {
                    $args[$key] = $signature->default;
                }
            }

            if (!$signature->type->accepts($args[$key], $messages)) {
                throw new SuperArgumentException("$key: " . implode(' ', $messages));
            }

            $newArgs[$key] = $args[$key];
        }

        $args = $newArgs;
    }

    public static function processArgument(SuperArgumentSignature $signature, &$arg) {
        if (!$signature->type->accepts($arg, $messages)) {
            throw new SuperArgumentException(implode(' ', $messages));
        }
    }

}