<?php

namespace Xua\Core\Eves;

use Xua\Core\Supers\Highers\Map;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Tools\Entity\Condition;
use Xua\Core\Tools\Entity\EntityArray;
use Xua\Core\Tools\Entity\Order;
use Xua\Core\Tools\Entity\Pager;
use Xua\Core\Tools\Signature\EntityFieldSignature;
use Xua\Core\Tools\Signature\MethodItemSignature;
use Xua\Core\Tools\Signature\VarqueMethodFieldSignature;

abstract class MethodQuery extends MethodEve
{
    /** @var Entity[] $_cache_feed */
    private ?array $_cache_feed = null;

    # Finalize Eve Methods
    final protected static function requestSignaturesCalculator(): array
    {
        return parent::requestSignaturesCalculator();
    }

    final protected static function responseSignaturesCalculator(): array
    {
        $fields = static::fields();
        $fieldsType = [];
        foreach ($fields as $field) {
            $fieldsType[$field->root->name()] = $field->root->type();
        }
        $fieldsType = new StructuredMap(['structure' => $fieldsType]);
        $association = static::association();
        return array_merge(parent::responseSignaturesCalculator(), [
            static::wrapper() => new MethodItemSignature(
                $association
                    ? new Map(['keyType' => $association->type, 'valueType' => $fieldsType])
                    : new Sequence(['type' => $fieldsType]),
                true, null, false
            ),
        ]);
    }

    protected function body(): void
    {
        $feed = $this->feed();
        $fields = static::fields();
        $association = static::association();

        $this->{static::wrapper()} = EntityArray::manyToArray($feed, $fields, $association);
    }

    # Overridable Methods Wrappers

    /**
     * @return Entity[]
     */
    final protected function feed(): array {
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

    /**
     * @return VarqueMethodFieldSignature[]
     */
    protected static function fields(): array
    {
        return [];
    }

    /**
     * @return Entity[]
     */
    protected function _feed(): array {
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

    protected static function wrapper(): string
    {
        return 'result';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return null;
    }
}