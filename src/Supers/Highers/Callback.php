<?php

namespace Xua\Core\Supers\Highers;

use Closure;
use ReflectionFunction;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Supers\Trilean;
use Xua\Core\Supers\Universal;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?array parameters
 * @property ?string returnType
 * @property bool allowSubtype
 * @property bool nullable
 */
class Callback extends Super
{
    const parameters = self::class . '::parameters';
    const returnType = self::class . '::returnType';
    const allowSubtype = self::class . '::allowSubtype';
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::parameters, false, null,
                new Sequence([
                    Sequence::type => new StructuredMap([
                        StructuredMap::structure => [
                            'name' => new Symbol([Symbol::nullable => true]),
                            'type' => new Text([Text::nullable => true]),
                            'allowSubtype' => new Boolean([]),
                            'required' => new Trilean([]),
                            'checkDefault' => new Boolean([]),
                            'default' => new Universal([]),
                            'passByReference' => new Trilean([]),
                        ],
                        StructuredMap::nullable => true
                    ]),
                    Sequence::nullable => true,
                ])
            ),
            Signature::new(false, static::returnType, false, null,
                new Text([Text::nullable => true])
            ),
            Signature::new(false, static::allowSubtype, false, false,
                new Boolean([])
            ),
            Signature::new(false, static::nullable, false, false,
                new Boolean([])
            ),
            ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_callable($input)) {
            $message = gettype($input) . " is not callable.";
            return false;
        }

        $function = new ReflectionFunction(Closure::fromCallable($input));

        $returnType = $function->getReturnType();
        if ($this->returnType !== null and ($this->allowSubtype ? (is_a($returnType, $this->returnType)) : ($this->returnType != $returnType))) {
            $message = "Return type '$this->returnType' expected, got '$returnType'.";
            return false;
        }

        $parameters = $function->getParameters();

        if ($this->parameters !== null) {
            if (count($parameters) != count($this->parameters)) {
                $message = count($this->parameters) . " parameters expected, got " . count($parameters) . ".";
                return false;
            }

            for ($i = 0; $i < count($parameters); $i++) {
                $expectedParameterName = $this->parameters[$i]['name'];
                $gotParameterName = $parameters[$i]->getName();
                if ($expectedParameterName !== null and $expectedParameterName != $gotParameterName) {
                    $message = "The name of $i-th parameter must be '$expectedParameterName', but is '$gotParameterName'.";
                    return false;
                }

                $expectedParameterType = $this->parameters[$i]['type'];
                $gotParameterType = $parameters[$i]->getType();
                if ($expectedParameterType !== null and ($this->parameters[$i]['allowSubtype'] ? (is_a($gotParameterType, $expectedParameterType)) : ($expectedParameterType != $gotParameterType))) {
                    $message = "The type of $i-th parameter must be '$expectedParameterType', but is '$gotParameterType'.";
                    return false;
                }

                $expectedParameterRequired = $this->parameters[$i]['required'];
                $gotParameterIsDefaultAvailable = $parameters[$i]->isDefaultValueAvailable();
                if ($expectedParameterRequired !== null and $expectedParameterRequired != !$gotParameterIsDefaultAvailable) {
                    $message = $expectedParameterRequired
                        ? "$i-th parameter is expected to be required but has a default value."
                        : "$i-th parameter is not expected to be required but has not a default value.";
                    return false;
                }

                if ($this->parameters[$i]['checkDefault']) {
                    $expectedParameterDefault = $this->parameters[$i]['default'];
                    $gotParameterDefault = $parameters[$i]->getDefaultValue();
                    if (!$gotParameterIsDefaultAvailable) {
                        $message = "$i-th parameter is expected to have default value of '" .
                            xua_var_dump($expectedParameterDefault) .
                            "' but no default value is provided.";
                        return false;
                    }

                    if ($expectedParameterDefault != $gotParameterDefault) {
                        $message = "$i-th parameter is expected to have default value of '" .
                            xua_var_dump($expectedParameterDefault) . "' but '" .
                            xua_var_dump($gotParameterDefault) . "' default value is provided.";
                        return false;
                    }
                }

                $expectedParameterPassByReference = $this->parameters[$i]['passByReference'];
                $gotParameterPassByReference = $parameters[$i]->isPassedByReference();
                if ($expectedParameterPassByReference !== null and $expectedParameterPassByReference != $gotParameterPassByReference) {
                    $message = "$i-th parameter is expected to be pass-by-reference but is not.";
                    return false;
                }
            }
        }
        
        return true;
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . 'callable';
    }
}