<?php

namespace Xua\Core\Tools\Entity;

class Order
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    private array $orders = [];
    private array $columns = [];

    private function __construct() {}

    public static function noOrder(): static
    {
        return new static();
    }

    public function addRaw(string $order): static
    {
        $this->orders[] = $order;
        return $this;
    }

    public function add(CF $field, string $direction): static
    {
        $this->columns[] = $field;
        return $this->addRaw($field->name() . ' ' . $direction);
    }

    public function addRandom(): static
    {
        return $this->addRaw('RAND()');
    }

    public function render(): string
    {
        if (!$this->orders) {
            return '';
        }
        return 'ORDER BY ' . implode(', ', $this->orders);
    }

    public function columnsExpression(): string
    {
        return implode(', ', array_map(function (CF $field) { return $field->name(); }, $this->columns));
    }
}