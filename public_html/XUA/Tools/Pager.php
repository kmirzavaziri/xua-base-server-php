<?php


namespace XUA\Tools;



class Pager
{
    public function __construct(
        public int $pageSize,
        public int $pageIndex
    )
    {}

    public static function unlimited() : Pager
    {
        return new Pager(0, 0);
    }
}