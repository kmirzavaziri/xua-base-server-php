<?php


namespace XUA\Supers\Highers;

use Closure;
use ReflectionFunction;
use XUA\Supers\Boolean;
use XUA\Supers\Strings\Symbol;
use XUA\Supers\Strings\Text;
use XUA\Supers\Trilean;
use XUA\Supers\Universal;
use XUA\Eves\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?array parameters
 * @method static SuperArgumentSignature A_parameters() The Signature of: Argument `parameters`
 * @property ?string returnType
 * @method static SuperArgumentSignature A_returnType() The Signature of: Argument `returnType`
 * @property bool allowSubtype
 * @method static SuperArgumentSignature A_allowSubtype() The Signature of: Argument `allowSubtype`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Callback extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'parameters' => new SuperArgumentSignature(new Sequence([
                'type' => new StructuredMap([
                    'structure' => [
                        'name' => new Symbol(['nullable' => true]),
                        'type' => new Text(['nullable' => true]),
                        'allowSubtype' => new Boolean([]),
                        'required' => new Trilean([]),
                        'checkDefault' => new Boolean([]),
                        'default' => new Universal([]),
                        'passByReference' => new Trilean([]),
                    ],
                    'nullable' => true
                ]),
                'nullable' => true,
            ]), false, null, false),
            'returnType' => new SuperArgumentSignature(new Text(['nullable' => true]), false, null, false),
            'allowSubtype' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
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