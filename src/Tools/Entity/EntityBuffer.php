<?php

namespace XUA\Tools\Entity;


use JetBrains\PhpStorm\Pure;
use PDOException;
use XUA\Eves\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\Services\ExpressionService;

final class EntityBuffer {
    /**
     * @var QueryBind[]
     */
    private array $queryBinds = [];

    /**
     * @param QueryBind[] $queryBinds
     * @return static
     */
    #[Pure] public static function fromQueryBinds(array $queryBinds): self
    {
        $return = new self();
        $return->queryBinds = $queryBinds;
        return $return;
    }

    public function store()
    {
        foreach ($this->queryBinds as $queryBind) {
            try {
                Entity::execute($queryBind->query, $queryBind->bind);
            } catch (PDOException $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $pattern = "/Duplicate entry '([^']*)' for key '([^.]*)\.([^']*)'/";
                    preg_match($pattern, $e->getMessage(), $matches);
                    $duplicateValues = explode('-', $matches[1]);
                    $table = $matches[2];
                    $duplicateIndexName = $matches[3];
                    /** @noinspection PhpUndefinedMethodInspection */
                    $duplicateIndexes = array_filter($queryBind->entity::indexes(), function (Index $index) use($duplicateIndexName) {
                        return $index->name == $duplicateIndexName;
                    });
                    $duplicateIndex = array_pop($duplicateIndexes);
                    $duplicateExpressions = [];
                    $iterator = 0;
                    $fieldNames = array_keys($duplicateIndex->fields);
                    foreach ($fieldNames as $fieldName) {
                        $duplicateExpressions[] = ExpressionService::get('entity.column.equal.to.value', [
                            'column' => ExpressionService::get('entityclass.' . $table . '.' . $fieldName),
                            'value' => $duplicateValues[$iterator],
                        ]);
                        $iterator++;
                    }
                    throw (new EntityFieldException())->setError($fieldNames[0], ExpressionService::get('errormessage.a.entity.with.expression.already.exists', [
                        'entity' => ExpressionService::get('entityclass.' . $table),
                        'expression' => implode(', ', $duplicateExpressions),
                    ]));
                } else {
                    throw $e;
                }
            }

        }
    }
}