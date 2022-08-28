<?php

namespace Xua\Core\Eves;

/**
 * Request *************************************************************************************************************
 * ---
 * Response ************************************************************************************************************
 * ---
 */
abstract class MethodRemove extends MethodEve
{
    /* Request ****************************************************************************************************** */
    /* --- */
    /* Response ***************************************************************************************************** */
    /* --- */
    /* ************************************************************************************************************** */

    private ?Entity $_cache_feed = null;

    protected function body(): void
    {
        $this->feed()->delete(static::force());
    }

    final protected function feed(): Entity {
        if ($this->_cache_feed === null) {
            $this->_cache_feed = $this->_feed();
        }
        return $this->_cache_feed;
    }

    protected function entity(): string
    {
        return Entity::class;
    }

    abstract protected function _feed(): Entity;

    protected static function force(): bool
    {
        return false;
    }
}