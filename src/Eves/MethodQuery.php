<?php

namespace Xua\Core\Eves;

use Xua\Core\Supers\Highers\Map;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Special\EntityFieldScheme;
use Xua\Core\Tools\Entity\Condition;
use Xua\Core\Tools\Entity\EntityArray;
use Xua\Core\Tools\Entity\Order;
use Xua\Core\Tools\Entity\Pager;
use Xua\Core\Tools\Signature\Signature;

/**
 * Request *************************************************************************************************************
 * ---
 * Response ************************************************************************************************************
 * @property array result
 */
abstract class MethodQuery extends FieldedMethod
{
    /* Request ****************************************************************************************************** */
    /* --- */
    /* Response ***************************************************************************************************** */
    const result = self::class . '::result';
    /* ************************************************************************************************************** */

    /** @var Entity[] $_cache_feed */
    private ?array $_cache_feed = null;

    protected static function _responseSignatures(): array
    {
        $structure = [];
        foreach (static::fieldSignatures() as $field) {
            /** @var EntityFieldScheme $scheme */
            $scheme = $field->declaration;
            $structure[$scheme->name] = $scheme->type;
        }
        $association = static::association();
        $itemSignature = new StructuredMap(['structure' => $structure]);
        $wrapperType = $association
            ? new Map(['keyType' => $association->declaration, 'valueType' => $itemSignature])
            : new Sequence(['type' => $itemSignature]);

        return array_merge(parent::_responseSignatures(), [
            Signature::new(false, static::result, true, null, $wrapperType)
        ]);
    }

    protected function body(): void
    {
        $this->result = EntityArray::manyToArray(
            $this->feed(),
            array_map(function (Signature $signature) { return $signature->declaration; }, static::fieldSignatures()),
            static::association()
        );
    }

    protected static function entity(): string
    {
        return Entity::class;
    }

    /**
     * @return Entity[]
     */
    final protected function feed(): array {
        if ($this->_cache_feed === null) {
            $this->_cache_feed = $this->_feed();
        }
        return $this->_cache_feed;
    }

    /**
     * @return Entity[]
     */
    protected function _feed(): array {
        /** @noinspection PhpUndefinedMethodInspection */
        return static::entity()::getMany(static::condition(), static::order(), static::pager());
    }

    protected function condition(): Condition {
        return Condition::trueLeaf();
    }

    protected function order(): Order {
        return Order::noOrder();
    }

    protected function pager(): Pager {
        return Pager::unlimited();
    }

    protected static function association(): ?Signature
    {
        return null;
    }
}