<?php

namespace Xua\Core\Eves;

use Xua\Core\Tools\Signature\Signature;

/**
 * Request *************************************************************************************************************
 * ---
 * Response ************************************************************************************************************
 * ---
 */
abstract class FieldedMethod extends MethodEve {
    /**
     * @return Signature[]
     */
    protected static function fieldSignatures() : array
    {
        return [];
    }
}