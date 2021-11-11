<?php

namespace Xua\Core\Tools\Entity;

use PDO;
use PDOException;
use PDOStatement;
use Xua\Core\Eves\Entity;
use Xua\Core\Supers\Special\OrderScheme;
use Xua\Core\Tools\Signature\Signature;

final class TableScheme
{
    /**
     * @param string $tableName
     * @param Column[] $columns
     * @param Signature[] $indexSignatures
     */
    public function __construct(
        public string $tableName,
        private array $columns,
        private array $indexSignatures,
    ) {}

    public function alter() : string
    {
        try {
            $rawOldColumns = Entity::connection()->query("DESCRIBE `$this->tableName`", PDO::FETCH_CLASS, Column::class);
        } catch (PDOException $e) {
            if (!str_contains($e->getMessage(), "doesn't exist")) {
                throw $e;
            }
            // Specified by variable $rawOldColumns being undefined
        }

        if (!isset($rawOldColumns)) {
            $this->addNewTable();
            return "";
        } else {
            $columnsAlter = $this->columnsAlter($rawOldColumns);

            $rawOldIndexes = Entity::execute("SHOW INDEX FROM `$this->tableName`");
            $indexesAlter = $this->indexesAlter($rawOldIndexes);

            if (!$columnsAlter and !$indexesAlter) {
                return "";
            }

            return "ALTER TABLE `$this->tableName`$columnsAlter$indexesAlter\nENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;";
        }
    }

    public static function getDropTables(array $newTables): string
    {
        $oldTables = array_map(function (array $oneTableArray) {
            return $oneTableArray[0];
        }, Entity::connection()->query("SHOW TABLES", PDO::FETCH_NUM)->fetchAll());
        $dropTables = array_diff($oldTables, $newTables);
        $drops = [];
        foreach ($dropTables as $dropTable) {
            $drops[] = "DROP TABLE $dropTable;";
        }
        return implode(PHP_EOL . PHP_EOL, $drops);
    }

    private function addNewTable(): void
    {
        $columns = implode(",\n\t", array_map(function (Column $column) {
            return $column->toQuery();
        }, $this->columns));

        $indexes = implode(",\n\t", array_map(function (Signature $signature) {
            /** @var OrderScheme $scheme */
            $scheme = $signature->declaration;
            return self::indexToQuery($scheme);
        }, $this->indexSignatures));

        Entity::execute("CREATE TABLE `$this->tableName` ($columns,\n\t$indexes)");
    }

    private function columnsAlter(false|PDOStatement $rawOldColumns): string
    {
        $oldColumns = [];
        foreach ($rawOldColumns as $rawOldColumn) {
            $oldColumns[$rawOldColumn->Field] = $rawOldColumn;
        }

        $newColumns = $this->columns;
        $removedColumns = array_diff(array_keys($oldColumns), array_keys($newColumns));
        $freshColumns = array_diff(array_keys($newColumns), array_keys($oldColumns));
        $potentiallyChangedColumns = array_intersect(array_keys($oldColumns), array_keys($newColumns));

        $orderChangeColumns = self::getOrderChanges(array_merge($potentiallyChangedColumns, $freshColumns), array_keys($newColumns));

        $changedColumns = [];
        foreach ($potentiallyChangedColumns as $key) {
            if (!$newColumns[$key]->eq($oldColumns[$key])) {
                $changedColumns[] = $key;
            }
        }

        $adds = [];
        foreach ($freshColumns as $column) {
            $tmpOrder = '';
            if (isset($orderChangeColumns[$column])) {
                $tmpOrder = ' ' . $orderChangeColumns[$column];
                unset($orderChangeColumns[$column]);
            }
            $adds[] =
                "ADD " . $newColumns[$column]->toQuery() . $tmpOrder . ", # NEW COLUMN";
        }
        $adds = $adds ? "\n\t" . implode("\n\t", $adds) : "";

        $changes = [];
        foreach ($changedColumns as $column) {
            $tmpOrder = '';
            if (isset($orderChangeColumns[$column])) {
                $tmpOrder = ' ' . $orderChangeColumns[$column];
                unset($orderChangeColumns[$column]);
            }
            $changes[] =
                "CHANGE COLUMN $column " . $newColumns[$column]->toQuery() . $tmpOrder .", # CHANGED FROM " . $oldColumns[$column]->toQuery();
        }
        $changes = $changes ? "\n\t" . implode("\n\t", $changes) : "";

        $drops = [];
        foreach ($removedColumns as $column) {
            $drops[] = "DROP COLUMN $column, # DROP COLUMN";
        }
        $drops = $drops ? "\n\t" . implode("\n\t", $drops) : "";

        $orderChanges = [];
        foreach ($orderChangeColumns as $column => $change) {
            $orderChanges[] = "CHANGE COLUMN $column " . $oldColumns[$column]->toQuery() . " $change, # CHANGE ORDER";
        }
        $orderChanges = $orderChanges ? "\n\t" . implode("\n\t", $orderChanges) : "";

        return "$drops$adds$changes$orderChanges";
    }

    private function indexesAlter(false|PDOStatement $rawOldIndexes): string
    {
        $oldIndexesData = [];
        foreach ($rawOldIndexes as $rawOldIndex) {
            if (!isset($oldIndexesData[$rawOldIndex['Key_name']])) {
                $oldIndexesData[$rawOldIndex['Key_name']] = [
                    'fields' => [],
                    'unique' => !$rawOldIndex['Non_unique'],
                ];
            }
            $oldIndexesData[$rawOldIndex['Key_name']]['fields'][$rawOldIndex['Seq_in_index'] - 1] = [
                OrderScheme::direction => $rawOldIndex['Collation'] == 'A' ? OrderScheme::DIRECTION_ASC : OrderScheme::DIRECTION_DESC,
                OrderScheme::field => $rawOldIndex['Column_name'],
            ];
        }
        $oldIndexes = [];
        foreach ($oldIndexesData as $key => $oldIndexData) {
            $oldIndexes[$key] = Signature::new(null, null, null, null, new OrderScheme([
                OrderScheme::fields => $oldIndexData['fields'],
                OrderScheme::unique => $oldIndexData['unique'],
                OrderScheme::name => $key
            ]));
        }

        $newIndexesSeq = $this->indexSignatures;
        $newIndexes = [];
        foreach ($newIndexesSeq as $index) {
            $newIndexes[$index->declaration->name] = $index;
        }

        $removedIndexes = [];
        foreach ($oldIndexes as $indexName => $oldIndex) {
            if (!in_array($oldIndex, $newIndexes)) {
                $removedIndexes[] = $indexName;
            }
        }

        $freshIndexes = [];
        foreach ($newIndexes as $indexName => $newIndex) {
            if (!in_array($newIndex, $oldIndexes)) {
                $freshIndexes[$indexName] = $newIndex;
            }
        }

        $adds = [];
        foreach ($freshIndexes as $index) {
            /** @var OrderScheme $scheme */
            $scheme = $index->declaration;
            $adds[] = "ADD " . self::indexToQuery($scheme) . ",";
        }
        $adds = $adds ? "\n\t" . implode("\n\t", $adds) : "";

        $drops = [];
        foreach ($removedIndexes as $indexName) {
            if ($indexName == 'PRIMARY') {
                $drops[] = "DROP PRIMARY KEY,";
            } else {
                $drops[] = "DROP INDEX $indexName,";
            }
        }
        $drops = $drops ? "\n\t" . implode("\n\t", $drops) : "";

        return "$drops$adds";
    }

    private static function getOrderChanges(array $oldKeys, array $newKeys): array
    {
        $order = [];
        $i = 0;
        foreach ($newKeys as $key) {
            $order[$key] = $i++;
        }

        $sequences = [];
        foreach ($oldKeys as $index => $key)
        {
            $sequences[$index][$key] = true;
            foreach ($sequences as $i => $sequence)
            {
                if ($order[$key] > $order[array_key_last($sequence)]) {
                    $sequences[$i][$key] = true;
                }
            }
        }
        $longestSequenceIndex = 0;
        foreach ($sequences as $index => $sequence)
        {
            if (count($sequence) > count($sequences[$longestSequenceIndex])) {
                $longestSequenceIndex = $index;
            }
        }
        $LIS = $sequences[$longestSequenceIndex];

        $result = [];
        foreach ($order as $key => $ord) {
            if (!isset($LIS[$key])) {
                $result[$key] = $ord == 0 ? 'FIRST' : 'AFTER ' . $newKeys[$ord - 1];
            }
        }

        return $result;
    }

    private static function indexToQuery(OrderScheme $scheme) : string
    {
        $fieldExpression = [];
        foreach ($scheme->fields as $field) {
            $fieldExpression[] = '`' . $field[OrderScheme::field] . '` ' . $field[OrderScheme::direction];
        }
        if ($scheme->name == 'PRIMARY') {
            return 'PRIMARY KEY (' . implode(', ', $fieldExpression) . ')';
        }
        return ($scheme->unique ? 'UNIQUE ' : '') . 'INDEX ' . $scheme->name . ' (' . implode(', ', $fieldExpression) . ')';
    }
}