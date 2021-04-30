<?php


namespace XUA\Tools;


final class EntityRelObject
{
    public function __construct(private string $__field) {}

    public function __get(string $name)
    {
        return $this->entity() . '.' . $name;
    }

    public function entity(): string
    {
        return $this->_x_field . "RelatedEntity";
    }
}