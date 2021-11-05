<?php

namespace Xua\Core\Tools\Entity;

final class Query {
    private function __construct(
        public string $query,
        public array $bind,
    ) {}

    public static function insert(string $table, array $data): self
    {
        $columns = [];
        $placeHolders = [];
        $bind = [];
        foreach ($data as $key => $value) {
            $columns[] = '`' . $key . '`';
            $placeHolders[] = '?';
            $bind[] = $value;
        }
        $columns = implode(', ', $columns);
        $placeHolders = implode(', ', $placeHolders);

        return new Query("INSERT INTO `$table` ($columns) VALUES ($placeHolders)", $bind);
    }

    public static function update(string $table, array $data, Condition $condition): self
    {
        $expressions = [];
        $bind = [];
        foreach ($data as $key => $value) {
            $expressions[] = "`$table`.`$key` = ?";
            $bind[] = $value;
        }
        $expressions = implode(', ', $expressions);
        $bind = array_merge($bind , $condition->parameters);
        $joins = $condition->joins();
        return new Query("UPDATE `$table` $joins SET $expressions WHERE $condition->template", $bind);
    }

    public static function delete(string $table, Condition $condition): self
    {
        $joins = $condition->joins();
        return new Query("DELETE `$table` FROM `$table` $joins WHERE $condition->template", $condition->parameters);
    }

    public static function insertMany(string $table, array $columns, array $rows): self
    {
        $placeHolders = implode(",\n", array_fill(0, count($rows), "\t" . implode(', ', array_fill(0, count($columns), '?'))));

        $bind = [];
        foreach ($rows as $row) {
            $bind = array_merge($bind, $row);
        }

        return new Query("INSERT INTO `$table` ($columns) VALUES \n$placeHolders", $bind);
    }

}