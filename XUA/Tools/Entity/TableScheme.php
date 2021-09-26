<?php


namespace XUA\Tools\Entity;


use PDO;
use PDOException;
use PDOStatement;
use XUA\Entity;

final class TableScheme
{
    public function __construct(
        public string $tableName,
        private array $columns,
        private array $indexes,
    ) {}

    private function addNewTable(): void
    {
        $columns = implode(",\n\t", array_map(function (Column $column) {
            return $column->toQuery();
        }, $this->columns));

        $indexes = implode(",\n\t", array_map(function (Index $index) {
            return $index->toQuery();
        }, $this->indexes));

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
        $oldIndexes = [];
        foreach ($rawOldIndexes as $rawOldIndex) {
            if (!isset($oldIndexes[$rawOldIndex['Key_name']])) {
                $oldIndexes[$rawOldIndex['Key_name']] = new Index([], !$rawOldIndex['Non_unique'], $rawOldIndex['Key_name']);
            }
            $oldIndexes[$rawOldIndex['Key_name']]->fields[$rawOldIndex['Seq_in_index']] = $rawOldIndex['Column_name'];
        }
        foreach ($oldIndexes as $oldIndex) {
            $tmp = [];
            foreach ($oldIndex->fields as $field) {
                $tmp[$field] = Index::ASC;
            }
            $oldIndex->fields = $tmp;
        }

        $newIndexesSeq = $this->indexes;
        $newIndexes = [];
        foreach ($newIndexesSeq as $index) {
            $newIndexes[$index->name] = $index;
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
            $adds[] = "ADD " . $index->toQuery() . ",";
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

    public function alter() : string
    {
        try {
            $rawOldColumns = Entity::connection()->query("DESCRIBE `$this->tableName`", PDO::FETCH_CLASS, Column::class);
        } catch (PDOException $e) {
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

            return "ALTER TABLE $this->tableName$columnsAlter$indexesAlter\nENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;";
        }
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
}