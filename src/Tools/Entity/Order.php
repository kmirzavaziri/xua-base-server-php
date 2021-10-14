<?php


namespace Xua\Core\Tools\Entity;

class Order
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    private array $orders = [];

    public static function noOrder() : static
    {
        return new static();
    }

    public function add(ConditionField $field, string $order) : static
    {
        $this->orders[] = [$field->name(), $order];
        return $this;
    }

    public function addRandom() : static
    {
        $this->orders[] = ['RAND()', ''];
        return $this;
    }

    public function render() : string
    {
        if (!$this->orders) {
            return '';
        }

        $orders = [];
        foreach ($this->orders as $order) {
            $orders[] = $order[0] . ' ' . $order[1];
        }
        return 'ORDER BY ' . implode(', ', $orders);
    }
}