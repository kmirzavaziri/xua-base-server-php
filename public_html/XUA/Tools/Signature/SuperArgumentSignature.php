<?php


namespace XUA\Tools\Signature;


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

    /**
     * @throws SuperArgumentException
     */
    public static function processArguments(array $signatures, array &$args) {
        $exception = new SuperArgumentException();
        $unknownKeys = array_diff(array_keys($args), array_keys($signatures));
        foreach ($unknownKeys as $unknownKey) {
            $exception->setError($unknownKey, 'Unknown argument');
        }
        $newArgs = [];
        foreach ($signatures as $key => $signature) {
            /** @var SuperArgumentSignature $signature */

            if (in_array($key, array_keys($args))) {
                if ($signature->const) {
                    $exception->setError($key, 'Cannot set constant argument');
                }
            } else {
                if ($signature->required) {
                    $exception->setError($key, 'Required argument not provided');
                } else {
                    $args[$key] = $signature->default;
                }
            }

            if (!$signature->type->accepts($args[$key], $messages)) {
                $exception->setError($key, implode(' ', $messages));
            }

            $newArgs[$key] = $args[$key];
        }

        if($exception->getErrors()) {
            throw $exception;
        }

        $args = $newArgs;
    }
}