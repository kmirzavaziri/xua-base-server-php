<?php


namespace Xua\Core\Tools\Entity;



class Pager
{
    public function __construct(
        public int $pageSize,
        public int $pageIndex
    ) {}

    public static function unlimited() : Pager
    {
        return new Pager(0, 0);
    }

    public function render(): string
    {
        if ($this->pageIndex < 1) {
            $this->pageIndex = 1;
        }
        if ($this->pageSize <= 0) {
            return '';
        }
        return "LIMIT $this->pageSize OFFSET " . ($this->pageSize * ($this->pageIndex - 1));
    }
}