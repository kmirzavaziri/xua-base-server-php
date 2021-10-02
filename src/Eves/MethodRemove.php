<?php

namespace XUA\Eves;

abstract class MethodRemove extends MethodEve
{
    private ?Entity $_cache_feed = null;

    # Finalize Eve Methods
    final protected static function responseSignaturesCalculator(): array
    {
        return parent::responseSignaturesCalculator();
    }

    protected function body(): void
    {
        $this->feed()->delete();
    }

    # Overridable Methods Wrappers
    final protected function feed(): Entity {
        if ($this->_cache_feed === null) {
            $this->_cache_feed = $this->_feed();
        }
        return $this->_cache_feed;
    }

    # New Overridable Methods
    protected static function entity(): string
    {
        return Entity::class;
    }

    abstract protected function _feed(): Entity;
}