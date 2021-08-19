<?php

namespace XUA\VARQUE;

use Exception;
use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use XUA\Entity;
use XUA\MethodEve;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Entity\EntityFieldSignatureTree;
use XUA\Tools\Entity\Order;
use XUA\Tools\Entity\Pager;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

abstract class MethodQuery extends MethodEve
{
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
            if (is_a($field, EntityFieldSignature::class)) {
                $fieldsType[$field->name] = $field->type;
            } elseif (is_a($field, EntityFieldSignatureTree::class)) {
                $fieldsType[$field->value->name] = $field->type();
            } else {
                throw new Exception('each field must be an instance of either EntityFieldSignature or EntityFieldSignatureTree');
            }
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
        $result = [];
        foreach ($feed as $entity) {
            $data = [];
            foreach ($fields as $field) {
                if (is_a($field, EntityFieldSignature::class)) {
                    $data[$field->name] = $entity->{$field->name};
                } elseif (is_a($field, EntityFieldSignatureTree::class)) {
                    $data[$field->value->name] = $field->value($entity);
                } else {
                    throw new Exception('each field must be an instance of either EntityFieldSignature or EntityFieldSignatureTree');
                }
            }
            if ($association) {
                $result[$entity->{$association->name}] = $data;
            } else {
                $result[] = $data;
            }
        }

        $this->{static::wrapper()} = $result;
    }

    # New Overridable Methods
    abstract protected static function entity(): string;

    abstract protected static function fields(): array;

    /**
     * @return Entity[]
     */
    protected function feed(): array {
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
        return lcfirst(static::entity()::table()) . 's';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return static::entity()::F_id();
    }
}