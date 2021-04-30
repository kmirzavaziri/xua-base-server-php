<?php


namespace XUA\Tools;



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