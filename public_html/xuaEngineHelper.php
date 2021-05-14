<?php

require 'magic.php';
require 'autoload.php';

$result = new Result(true, '');
try {
    $action = $argv[1];
    if ($action == 'getClassParameters') {
        $result = getClassParameters($argv[2]);
    } elseif ($action == 'getRelationInverseColumns') {
        $result = getRelationInverseColumns($argv[2]);
    } elseif ($action == 'getMethodExecuteVars') {
        $result = getMethodExecuteVars($argv[2]);
    } elseif ($action == 'getEntityConstants') {
        $result = getEntityConstants($argv[2]);
    } else {
        throw new Exception('unknown Action');
    }
} catch (Error $e) {
    $result->status = false;
    $result->result = $e->getMessage() . ' ' . $e->getTraceAsString();
}

echo json_encode($result);


class Result {
    function __construct(
        public bool $status,
        public mixed $result,
    )
    {}
}

function getClassParameters(string $class) : object
{
    $result = new Result(true, '');

    if (is_a($class, \XUA\Super::class, true)) {
        foreach ($class::formal() as $key => $signature) {
            $result->result .= " * @method SuperArgumentSignature $key" . PHP_EOL;
            $result->result .= " * @property " . $signature->type->phpType() . " $key" . PHP_EOL;
        }
        if ($result->result) {
            $result->result = "/**" . PHP_EOL . $result->result . " */" . PHP_EOL;
        }
    } elseif (is_a($class, \XUA\Entity::class, true)) {
        foreach ($class::structure() as $key => $signature) {
            $result->result .= " * @property " . $signature->type->phpType() . " $key" . PHP_EOL;
        }
        if ($result->result) {
            $result->result = "/**" . PHP_EOL . $result->result . " */" . PHP_EOL;
        }
    } elseif (is_a($class, \XUA\Method::class, true)) {
        foreach ($class::response() as $key => $signature) {
            $result->result .= " * @property " . $signature->type->phpType() . " $key" . PHP_EOL;
        }
        if ($result->result) {
            $result->result = "/**" . PHP_EOL . $result->result . " */" . PHP_EOL;
        }
    } else {
        $result->status = false;
        $result->result = "$class in not a super or entity.";
    }

    return $result;
}

function getRelationInverseColumns(string $class) : object
{
    $result = new Result(true, []);

    if (is_a($class, \XUA\Entity::class, true)) {
        foreach ($class::structure() as $key => $signature) {
            /** @var \XUA\Tools\Signature\EntityFieldSignature $signature */
            if (is_a($signature->type, \Supers\Basics\EntitySupers\EntityRelation::class) and $signature->type->definedOn == 'here') {
                $result->result[] = [
                    'new' => !in_array($signature->type->invName, array_keys($signature->type->relatedEntity::structure())),
                    'name' => $signature->type->invName,
                    'target' => $signature->type->relatedEntity,
                    'text' => "new EntityFieldSignature(
    static::class, '{$signature->type->invName}',
    new EntityRelation([
        'relatedEntity' => \\$signature->entity::class,
        'relation' => '" . strrev($signature->type->relation) . "',
        'invName' => '$key',
        'nullable' => " . ($signature->type->invNullable ? 'true' : 'false') . ",
        'invNullable' => " . ($signature->type->nullable ? 'true' : 'false') . ",
        'definedOn' => 'there',
    ]),
    null
)"
                ];
            }
        }
    } else {
        $result->status = false;
        $result->result = 'not entity';
    }

    return $result;
}

function getMethodExecuteVars(string $class) : object
{
    $result = new Result(true, '');

    if (is_a($class, \XUA\Method::class, true)) {
        foreach ($class::request() as $key => $signature) {
            $result->result .= '         * @var ' . $signature->type->phpType() . ' $' . $key . PHP_EOL;
        }
        if ($result->result) {
            $result->result = 'extract($request);' . PHP_EOL .
                '        /**' . PHP_EOL .
                '         **********************************************' . PHP_EOL .
                $result->result .
                '         **********************************************' . PHP_EOL .
                "         */" . PHP_EOL . PHP_EOL .
                '        ';
        }
    } else {
        $result->status = false;
        $result->result = 'not method';
    }

    return $result;
}

function getEntityConstants(string $class) : object
{
    $result = new Result(true, '');

    if (is_a($class, \XUA\Entity::class, true)) {
        foreach ($class::structure() as $key => $signature) {
            $result->result .= "const $key = '$signature->name';" . PHP_EOL . '    ';
        }
        $result->result = substr($result->result, 0, strlen($result->result) - 4) . PHP_EOL . '    ';
    } else {
        $result->status = false;
        $result->result = 'not entity';
    }

    return $result;
}