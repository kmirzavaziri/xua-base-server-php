<?php


namespace XUA\Tools\Entity;



class Order
{
    public static function noOrder() : Order
    {
        return new Order();
    }

    public function render() : string
    {
        return "";
    }
}