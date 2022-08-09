<?php

namespace Xua\Core\Eves;

use Xua\Core\Exceptions\DefinitionException;
use Xua\Core\Exceptions\EntityFieldException;
use Xua\Core\Supers\Special\EntityFieldScheme;
use Xua\Core\Tools\Entity\EntityBuffer;
use Xua\Core\Tools\Signature\Signature;
use Xua\Core\Tools\SignatureValueCalculator;

/**
 * Request *************************************************************************************************************
 * ---
 * Response ************************************************************************************************************
 * ---
 */
abstract class MethodAdjust extends FieldedMethod
{
    /* Request ****************************************************************************************************** */
    /* --- */
    /* Response ***************************************************************************************************** */
    /* --- */
    /* ************************************************************************************************************** */

    const STORE_BUFFER = 'store_buffer';
    const STORE_SELF = 'store_self';
    const DONT_STORE = 'dont_store';

    private ?Entity $_cache_feed = null;

    protected static function _requestSignatures(): array
    {
        $signatures = parent::_requestSignatures();
        foreach (static::fieldSignatures() as $field) {
            $signatures[] = Signature::new(
                $field->const,
                static::class . '::' . self::REQUEST_PREFIX . $field->declaration->name,
                $field->required,
                $field->default,
                $field->declaration->type
            );
        }
        return $signatures;
    }

    protected function body(): void
    {
        $feed = $this->feed();
        $fields = static::fieldSignatures();
        if ($this->storeMode() == self::STORE_BUFFER) {
            EntityBuffer::getEfficientBuffer()->add($feed);
        }
        foreach ($fields as $field) {
            /** @var EntityFieldScheme $scheme */
            $scheme = $field->declaration;
            try {
                if ($scheme->name == 'id') {
                    throw new DefinitionException('Cannot modify id');
                }
                SignatureValueCalculator::setEntityField($feed, $this->{MethodEve::REQUEST_PREFIX . $scheme->name}, $scheme, $this);
            } catch (EntityFieldException $e) {
                $this->_x_error->fromException($e);
                $this->throwError();
            }
        }
        try {
            if ($this->storeMode() == self::STORE_BUFFER) {
                EntityBuffer::getEfficientBuffer()->store();
            } elseif ($this->storeMode() == self::STORE_SELF) {
                $feed->store();
            }
        } catch (EntityFieldException $e) {
            throw $this->_x_error->fromException($e);
        }
    }

    protected static function entity(): string
    {
        return Entity::class;
    }

    final public function feed(): Entity {
        if ($this->_cache_feed === null) {
            $this->_cache_feed = $this->_feed();
        }
        return $this->_cache_feed;
    }

    abstract protected function _feed(): Entity;

    public function storeMode(): bool
    {
        return self::STORE_BUFFER;
    }
}