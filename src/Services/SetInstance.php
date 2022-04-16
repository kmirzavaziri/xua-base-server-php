<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

class SetInstance extends Service
{
    private array $_ = [];

    public static function fromString(string $string): self
    {
        return self::fromList($string ? explode(',', $string) : []);
    }

    public static function fromList(array $list): self
    {
        $set = new self();
        foreach ($list as $item) {
            $set->_[$item] = true;
        }
        return $set;
    }

    public function toList(): array
    {
        return array_keys($this->_);
    }

    public function toString(): string
    {
        return implode(',', array_keys($this->_));
    }

    public function empty(): bool {
        return !$this->_;
    }

    public function remove(string $item): void
    {
        unset($this->_[$item]);
    }

    public function add(string $item): void
    {
        $this->_[$item] = true;
    }

    public function has(string $item): bool
    {
        return isset($this->_[$item]);
    }

    public function minus(self $set): self
    {
        $r = clone $this;
        foreach ($set->toList() as $item) {
            $r->remove($item);
        }
        return $r;
    }
}