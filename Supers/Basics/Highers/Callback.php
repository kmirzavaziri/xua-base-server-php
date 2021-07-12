<?php


namespace Supers\Basics\Highers;



use Closure;
use ReflectionFunction;
use Supers\Basics\Boolean;
use Supers\Basics\Strings\Symbol;
use Supers\Basics\Strings\Text;
use Supers\Basics\Trilean;
use Supers\Basics\Universal;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?array parameters
 * @method static SuperArgumentSignature A_parameters() The Signature of: Argument `parameters`
 * @property ?string returnType
 * @method static SuperArgumentSignature A_returnType() The Signature of: Argument `returnType`
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
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
            ]);
    }

    protected function _predicate($input, string &$message = null): bool
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

        if ($this->returnType !== null and $this->returnType != $returnType) {
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
                if ($this->parameters[$i]['name'] !== null and $this->parameters[$i]['name'] != $parameters[$i]->getName()) {
                    $message = "The name of $i-th parameter must be '" . $this->parameters[$i]['name'] . "', but is '" . $parameters[$i]->getName() . "'.";
                    return false;
                }
                if ($this->parameters[$i]['type'] !== null and $this->parameters[$i]['type'] != $parameters[$i]->getType()) {
                    $message = "The type of $i-th parameter must be '" . $this->parameters[$i]['type'] . "', but is '" . $parameters[$i]->getType() . "'.";
                    return false;
                }
                if ($this->parameters[$i]['required'] !== null and $this->parameters[$i]['required'] != !$parameters[$i]->isDefaultValueAvailable()) {
                    $message = $this->parameters[$i]['required']
                        ? "$i-th parameter is expected to be required but has a default value."
                        : "$i-th parameter is not expected to be required but has not a default value.";
                    return false;
                }
                if ($this->parameters[$i]['checkDefault']) {
                    if (!$parameters[$i]->isDefaultValueAvailable()) {
                        $message = "$i-th parameter is expected to have default value of '" .
                            xua_var_dump($this->parameters[$i]['default']) .
                            "' but no default value is provided.";
                        return false;
                    }
                    if ($this->parameters[$i]['default'] != $parameters[$i]->getDefaultValue()) {
                        $message = "$i-th parameter is expected to have default value of '" .
                            xua_var_dump($this->parameters[$i]['default']) . "' but '" .
                            xua_var_dump($parameters[$i]->getDefaultValue()) . "' default value is provided.";
                        return false;
                    }
                }
                if ($this->parameters[$i]['passByReference'] !== null and $this->parameters[$i]['passByReference'] != $parameters[$i]->isPassedByReference()) {
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