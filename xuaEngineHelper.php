<?php

use Supers\Basics\EntitySupers\EntityRelation;
use XUA\Entity;
use XUA\MethodEve;
use XUA\Super;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\XUAException;

require 'magic.php';
require 'autoload.php';

chdir('/var/www');

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
} catch (Throwable $e) {
    $result->status = false;
    $result->result = (is_a($e, XUAException::class) ? xua_var_dump($e->getErrors()) : $e->getMessage()) . ' ' . $e->getTraceAsString();
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

    if (is_a($class, Super::class, true)) {
        foreach ($class::argumentSignatures() as $key => $signature) {
            $result->result .= " * @property " . $signature->type->phpType() . " $key" . PHP_EOL;
            $result->result .= " * @method static SuperArgumentSignature A_$key() The Signature of: Argument `$key`" . PHP_EOL;
        }
        if ($result->result) {
            $result->result = "/**" . PHP_EOL . $result->result . " */" . PHP_EOL;
        }
    } elseif (is_a($class, Entity::class, true)) {
        foreach ($class::fieldSignatures() as $key => $signature) {
            $result->result .= " * @property " . $signature->type->phpType() . " $key" . PHP_EOL;
            $result->result .= " * @method static EntityFieldSignature F_$key() The Signature of: Field `$key`" . PHP_EOL;
            $result->result .= " * @method static ConditionField C_$key() The Condition Field of: Field `$key`" . PHP_EOL;
        }
        if ($result->result) {
            $result->result = "/**" . PHP_EOL . $result->result . " */" . PHP_EOL;
        }
    } elseif (is_a($class, MethodEve::class, true)) {
        foreach ($class::requestSignatures() as $key => $signature) {
            $result->result .= " * @property " . $signature->type->phpType() . " Q_$key" . PHP_EOL;
            $result->result .= " * @method static MethodItemSignature Q_$key() The Signature of: Request Item `$key`" . PHP_EOL;
        }
        foreach ($class::responseSignatures() as $key => $signature) {
            $result->result .= " * @property " . $signature->type->phpType() . " $key" . PHP_EOL;
            $result->result .= " * @method static MethodItemSignature R_$key() The Signature of: Response Item `$key`" . PHP_EOL;
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

    if (is_a($class, Entity::class, true)) {
        foreach ($class::fieldSignatures() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if (is_a($signature->type, EntityRelation::class) and $signature->type->definedOn == 'here' and $signature->type->invName) {
                $result->result[] = [
                    'new' => !in_array($signature->type->invName, array_keys($signature->type->relatedEntity::fieldSignatures())),
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
    ". (strrev($signature->type->relation)[1] == 'N' ? '[]' : 'null') ."
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

    if (is_a($class, MethodEve::class, true)) {
        foreach ($class::requestSignatures() as $key => $signature) {
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

    if (is_a($class, Entity::class, true)) {
        foreach ($class::fieldSignatures() as $key => $signature) {
            $result->result .= "const $key = '$signature->name';" . PHP_EOL . '    ';
        }
        $result->result = substr($result->result, 0, strlen($result->result) - 4) . PHP_EOL . '    ';
    } else {
        $result->status = false;
        $result->result = 'not entity';
    }

    return $result;
}