<?php


namespace XUA\Tools;


use Exception;
use PDO;
use PDOStatement;
use XUA\Entity;

class TableScheme
{
    public function __construct(
        private string $tableName,
        private array $columns,
        private array $indexes,
    ) {
    }

    private function addNewTable(): void
    {
        $columns = implode(",\n\t", array_map(function (Column $column) {
            return $column->toQuery();
        }, $this->columns));

        $indexes = implode(",\n\t", array_map(function (Index $index) {
            return $index->toQuery();
        }, $this->indexes));

        Entity::connection()->query("CREATE TABLE $this->tableName ($columns,\n\t$indexes)");
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
        $potentiallyChangedColumns = array_intersect(array_keys($newColumns), array_keys($oldColumns));
        $changedColumns = [];
        foreach ($potentiallyChangedColumns as $key) {
            if (!$newColumns[$key]->eq($oldColumns[$key])) {
                $changedColumns[] = $key;
            }
        }

        $adds = [];
        foreach ($freshColumns as $column) {
            $adds[] = "ADD " . $newColumns[$column]->toQuery() . ",";
        }
        $adds = $adds ? "\n\t" . implode("\n\t", $adds) : "";

        $changes = [];
        foreach ($changedColumns as $column) {
            $changes[] = "CHANGE COLUMN $column " . $newColumns[$column]->toQuery() . ", # CHANGED FROM " . $oldColumns[$column]->toQuery();
        }
        $changes = $changes ? "\n\t" . implode("\n\t", $changes) : "";

        $drops = [];
        foreach ($removedColumns as $column) {
            $drops[] = "DROP COLUMN $column,";
        }
        $drops = $drops ? "\n\t" . implode("\n\t", $drops) : "";

        return "$drops$adds$changes";
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
        foreach ($oldIndexes as $key => $oldIndex) {
            $tmp = [];
            foreach ($oldIndex->fields as $field) {
                $tmp[$field] = Index::ASC;
            }
            $oldIndexes[$key]->fields = $tmp;
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
            $rawOldColumns = Entity::connection()->query("DESCRIBE $this->tableName", PDO::FETCH_CLASS, Column::class);
            // @TODO change this Exception to exact Exception Class the PDO throws.
        } catch (Exception $e) {
            // Specified by variable $rawOldColumns being undefined
        }

        if (!isset($rawOldColumns)) {
            $this->addNewTable();
            return "";
        } else {
            $columnsAlter = $this->columnsAlter($rawOldColumns);

            $rawOldIndexes = Entity::connection()->query("SHOW INDEX FROM $this->tableName");
            $indexesAlter = $this->indexesAlter($rawOldIndexes);

            if (!$columnsAlter and !$indexesAlter) {
                return "";
            }

            return "ALTER TABLE $this->tableName$columnsAlter$indexesAlter\nENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;";
        }
    }
}