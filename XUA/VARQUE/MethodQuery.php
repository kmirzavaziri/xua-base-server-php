<?php

namespace XUA\VARQUE;

use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Highers\StructuredMap;
use XUA\Entity;
use XUA\MethodEve;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\MethodItemSignature;

abstract class MethodQuery extends MethodEve
{
    final protected static function requestSignaturesCalculator(): array
    {
        return parent::requestSignaturesCalculator();
    }

    final protected static function responseSignaturesCalculator(): array
    {
        $fields = static::fields();
        $fieldsType = [];
        foreach ($fields as $field) {
            $fieldsType[$field->name] = $field->type;
        }
        $association = static::association();
        return array_merge(parent::responseSignaturesCalculator(), [
            static::wrapper() => new MethodItemSignature(
                $association
                    ? new Map(['keyType' => $association->type, 'valueType' => new StructuredMap(['structure' => $fieldsType])])
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
                $data[$field->name] = $entity->{$field->name};
            }
            if ($association) {
                $result[$entity->{$association->name}] = $data;
            } else {
                $result[] = $data;
            }
        }

        $this->{static::wrapper()} = $result;
    }

    abstract protected static function entity(): string;

    /**
     * @return EntityFieldSignature[]
     */
    abstract protected static function fields(): array;

    /**
     * @return Entity[]
     */
    abstract protected function feed(): array;

    protected static function wrapper(): string
    {
        return lcfirst(static::entity()) . 's';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return static::entity()::F_id();
    }
}