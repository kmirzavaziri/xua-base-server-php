<?php


namespace Xua\Core\Tools\Entity;

class Index
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    public function __construct(
        public array $fields,
        public bool $unique,
        public ?string $name = null,
    ) {
        if (!$this->name) {
            $fieldExpression = '';
            foreach ($this->fields as $field => $order) {
                $fieldExpression .= '_' . $field . '_' . strtolower($order);
            }
            $this->name = $fieldExpression . ($unique ? '_unique' : '') . '_index';
        }
    }

    public function toQuery() : string
    {
        $fieldExpression = [];
        foreach ($this->fields as $field => $order) {
            $fieldExpression[] = '`' . $field . '` ' . $order;
        }
        if ($this->name == 'PRIMARY') {
            return 'PRIMARY KEY (' . implode(', ', $fieldExpression) . ')';
        }

        return ($this->unique ? 'UNIQUE ' : '') . 'INDEX ' . $this->name . ' (' . implode(', ', $fieldExpression) . ')';
    }

    public function eq(Index $index) : bool
    {
        if ($this->name != $index->name) {
            return false;
        }
        if ($this->fields != $index->fields) {
            return false;
        }
        if ($this->unique != $index->unique) {
            return false;
        }

        return true;
    }

}