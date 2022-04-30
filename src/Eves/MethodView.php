<?php

namespace Xua\Core\Eves;

use Xua\Core\Supers\Highers\Nullable;
use Xua\Core\Supers\Special\EntityFieldScheme;
use Xua\Core\Tools\Signature\Signature;
use Xua\Core\Tools\SignatureValueCalculator;

/**
 * Request *************************************************************************************************************
 * ---
 * Response ************************************************************************************************************
 * ---
 */
abstract class MethodView extends FieldedMethod
{
    /* Request ****************************************************************************************************** */
    /* --- */
    /* Response ***************************************************************************************************** */
    /* --- */
    /* ************************************************************************************************************** */

    private ?Entity $_cache_feed = null;

    protected static function _responseSignatures(): array
    {
        $signatures = parent::_responseSignatures();
        foreach (static::fieldSignatures() as $field) {
            /** @var EntityFieldScheme $scheme */
            $scheme = $field->declaration;
            $signature = Signature::new(
                $field->const,
                static::class . '::' . self::RESPONSE_PREFIX . $scheme->name,
                $field->required,
                $field->default,
                new Nullable([Nullable::type => $scheme->type])
            );
            $signatures[] = $signature;
        }
        return $signatures;
    }

    protected function body(): void
    {
        $feed = $this->feed();
        if (!$feed) {
            return;
        }
        $fields = static::fieldSignatures();
        foreach ($fields as $field) {
            /** @var EntityFieldScheme $scheme */
            $scheme = $field->declaration;
            $this->{MethodEve::RESPONSE_PREFIX . $scheme->name} = SignatureValueCalculator::getEntityField($feed, $scheme, $this);
        }
    }

    protected static function entity(): string
    {
        return Entity::class;
    }

    final protected function feed(): Entity {
        if ($this->_cache_feed === null) {
            $this->_cache_feed = $this->_feed();
        }
        return $this->_cache_feed;
    }

    abstract protected function _feed(): Entity;
}