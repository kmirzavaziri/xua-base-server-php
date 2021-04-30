<?php


namespace XUA\Tools;


use Exception;
use PDO;
use XUA\Entity;

class TableScheme
{
    public function __construct(
        private string $tableName,
        private array $columns,
    ) {}

    public function alter() : string
    {
        $table = $this->tableName;
        $newColumns = $this->columns;

        $tableExists = true;
        $oldColumns = [];
        try {
            $rawOldColumns = Entity::connection()->query("DESCRIBE $table", PDO::FETCH_CLASS, Column::class);
            foreach ($rawOldColumns as $rawOldColumn) {
                $oldColumns[$rawOldColumn->Field] = $rawOldColumn;
            }
        } catch (Exception $e) {
            $tableExists = false;
        }

        if (!$tableExists) {
            $columns = implode(",\n\t", array_map(function (Column $column) {return $column->toQuery();}, $newColumns));
            Entity::connection()->query("CREATE TABLE $table ($columns)");
            return "";
        } else {
            $removedColumns = array_diff(array_keys($oldColumns), array_keys($newColumns));
            $freshColumns = array_diff(array_keys($newColumns), array_keys($oldColumns));
            $potentiallyChangedColumns = array_intersect(array_keys($newColumns), array_keys($oldColumns));
            $changedColumns = [];
            foreach ($potentiallyChangedColumns as $key) {
                if (! $newColumns[$key]->eq($oldColumns[$key])) {
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

            if (!$adds and !$changes and !$drops) {
                return "";
            }

            $query = "ALTER TABLE $table$adds$changes$drops" . PHP_EOL;

            # @TODO fix comma in comments
            $pos = strrpos($query, ',');
            if($pos !== false) {
                $query = substr_replace($query, ';', $pos, 1);
            }

            return $query;
        }
    }
}