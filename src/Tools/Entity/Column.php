<?php

namespace Xua\Core\Tools\Entity;

class Column
{
    private string $fieldName = '';
    private string $type = 'bool';
    private bool $nullable = false;
    private bool $required = true;
    private mixed $default = null;
    private string $extra = '';

    public function __set(string $name, $value): void
    {
        switch ($name) {
            case 'Field':
                $this->fieldName = $value;
                break;
            case 'Type':
                $this->type = $value;
                break;
            case 'Null':
                $this->nullable = $value == 'YES';
                break;
            case 'Default':
                // @TODO there must be a better solution
                $this->default = match (true) {
                    $this->type == 'datetime' and $value == 'CURRENT_TIMESTAMP' => new RawSQL($value),
                    $value === null => null,
                    ctype_digit($value) => (int)$value,
                    is_numeric($value) => (float)$value,
                    default => $value,
                };

                if ($this->default === null) {
                    $this->required = false;
                }
                break;
            case 'Extra':
                if (self::containsCaseInsensitive($value, 'AUTO_INCREMENT')) {
                    $this->extra .= 'auto_increment';
                }
                break;
        }
    }

    public static function fromQuery(string $fieldName, string $definition, bool $required = true, mixed $default = null): Column
    {
        $column = new Column();
        $column->fieldName = $fieldName;
        $definition = str_ireplace('NOT NULL', '', $definition);
        if (self::containsCaseInsensitive($definition, 'NULL')) {
            $column->nullable = true;
        }
        $definition = str_ireplace('NULL', '', $definition);
        if (self::containsCaseInsensitive($definition, 'AUTO_INCREMENT')) {
            $column->extra .= 'auto_increment';
        }
        $definition = str_ireplace('AUTO_INCREMENT', '', $definition);
        $column->type = strtolower(trim($definition));
        $column->required = $required;
        if (!in_array($column->type, ['blob', 'text', 'geometry', 'json'])) {
            // MySQL doesn't let these types to have a default value
            $column->default = $default;
        }
        return $column;
    }

    public function toQuery(): string
    {
        $nullExpression = $this->nullable ? 'NULL' : 'NOT NULL';
        $defaultExpression = '';
        $bind = [];
        if (!in_array($this->type, ['blob', 'text', 'geometry', 'json']) and !$this->required) {
            // MySQL doesn't let these types to have a default value
            $defaultExpression = 'DEFAULT ?';
            $bind[] = $this->default;
        }
        return QueryBinder::bind(trim("`$this->fieldName` $this->type $nullExpression $defaultExpression $this->extra"), $bind);
    }

    public function eq(Column $column): bool
    {
        if ($this->fieldName != $column->fieldName) {
            return false;
        }
        if ($this->type != $column->type) {
            return false;
        }
        if ($this->nullable != $column->nullable) {
            return false;
        }
        if (gettype($this->default) != gettype($column->default) or $this->default != $column->default) {
            return false;
        }
        if ($this->extra != $column->extra) {
            return false;
        }

        return true;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    private static function containsCaseInsensitive($haystack, $needle): bool
    {
        return stripos($haystack, $needle) !== false;
    }
}