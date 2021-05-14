<?php


namespace XUA\Tools\Entity;


class Column
{
    public string $Field;
    private string $Type;
    private string $Null = "NO";
    private string $Key = "";
    private string $Extra = "";

    private static function contains($haystack, $needle) : bool
    {
        return stripos($haystack, $needle) !== false;
    }

    public static function fromQuery(string $definition) : Column
    {
        $column = new Column();

        $column->Field = explode(" ", $definition)[0];

        $definition = strstr($definition, " ");
        if (self::contains($definition, 'NOT NULL')) {
            $column->Null = 'NO';
        }
        $definition = str_ireplace('NOT NULL', '', $definition);

        if (self::contains($definition, 'NULL')) {
            $column->Null = 'YES';
        }
        $definition = str_ireplace('NULL', '', $definition);

        if (self::contains($definition, 'AUTO_INCREMENT')) {
            $column->Extra .= 'auto_increment';
        }
        $definition = str_ireplace('AUTO_INCREMENT', '', $definition);

        $column->Type = strtolower(trim($definition));

        return $column;
    }

    public function toQuery() : string
    {
        $nullExpression = $this->Null == 'YES' ? 'NULL' : 'NOT NULL';
        return trim("$this->Field $this->Type $nullExpression $this->Extra");
    }

    public function eq(Column $column) : bool
    {
        if ($this->Field != $column->Field) {
            return false;
        }
        if ($this->Type != $column->Type) {
            return false;
        }
        if ($this->Null != $column->Null) {
            return false;
        }
        if ($this->Extra != $column->Extra) {
            return false;
        }

        return true;
    }
}