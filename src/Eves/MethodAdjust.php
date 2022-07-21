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
        EntityBuffer::getEfficientBuffer()->add($feed);
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
        if (!$this->dontStore()) {
            try {
                EntityBuffer::getEfficientBuffer()->store();
            } catch (EntityFieldException $e) {
                throw $this->_x_error->fromException($e);
            }
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

    public function dontStore() : bool
    {
        return false; // @TODO we better have a third option to store only this method's feed and not all of the entity buffer (or we can find a way to guess it since only the main (entry point) of methods must store all of the entity buffer at the end)
    }
}