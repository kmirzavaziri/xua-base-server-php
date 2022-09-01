<?php

namespace Xua\Core\Eves;

use Xua\Core\Exceptions\EntityConditionException;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Special\ConditionScheme;
use Xua\Core\Supers\Special\OrderScheme;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Tools\Entity\CF;
use Xua\Core\Tools\Entity\Condition;
use Xua\Core\Tools\Entity\EntityArray;
use Xua\Core\Tools\Entity\Order;
use Xua\Core\Tools\Entity\Pager;
use Xua\Core\Tools\Signature\Signature;

/**
 * Request *************************************************************************************************************
 * @property ?string Q_order
 * @property ?array Q_filters
 * Response ************************************************************************************************************
 * @property array[] result
 * *********************************************************************************************************************
 */
abstract class MethodQuery extends FieldedMethod
{
    /* Request ****************************************************************************************************** */
    const Q_order = self::class . '::Q_order';
    const Q_filters = self::class . '::Q_filters';
    /* Response ***************************************************************************************************** */
    const result = self::class . '::result';
    /* ************************************************************************************************************** */

    /** @var Entity[] $_cache_feed */
    private ?array $_cache_feed = null;

    protected static function _requestSignatures(): array
    {
        $filtersStructure = [];
        foreach (static::filterSignatures() as $signature) {
            $filtersStructure[$signature->name] = $signature->declaration;
        }
        return array_merge(parent::_responseSignatures(), [
            Signature::new(false, static::Q_order, false, null,
                new Enum([
                    Enum::nullable => true,
                    Enum::values => array_map(function (Signature $signature) { return $signature->name; }, static::orderSignatures()),
                ])
            ),
            Signature::new(false, static::Q_filters, false, null,
                new StructuredMap([
                    StructuredMap::nullable => true,
                    StructuredMap::structure => $filtersStructure,
                    StructuredMap::optionalKeys => true,
                ])
            ),
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            Signature::new(false, static::result, true, null, EntityArray::manyToArrayType(
                array_map(function (Signature $signature) { return $signature->declaration; }, static::fieldSignatures()),
                static::association()?->declaration
            ))
        ]);
    }

    /**
     * @return Signature[]
     */
    protected static function orderSignatures() : array
    {
        return [];
    }

    /**
     * @return Signature[]
     */
    protected static function filterSignatures() : array
    {
        return [];
    }

    protected function body(): void
    {
        $this->result = EntityArray::manyToArray(
            $this->feed(),
            array_map(function (Signature $signature) { return $signature->declaration; }, static::fieldSignatures()),
            static::association(),
            $this,
        );
    }

    protected function entity(): string
    {
        return Entity::class;
    }

    /**
     * @return Entity[]
     */
    final public function feed(): array {
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
        return $this->entity()::getMany(static::condition(), static::order(), static::pager());
    }

    protected function condition(): Condition {
        $condition = Condition::trueLeaf();
        if ($this->Q_filters) {
            foreach (static::filterSignatures() as $signature) {
                if ($this->Q_filters[$signature->name] ?? false) {
                    /** @var ConditionScheme $conditionScheme */
                    $conditionScheme = $signature->declaration;
                    try {
                        $condition->andC(($conditionScheme->conditionGenerator)($this->Q_filters[$signature->name]));
                    } catch (EntityConditionException $e) {
                        $this->addAndThrowError(static::Q_filters, $e->getErrors());
                    }
                }
            }
        }
        return $condition;
    }

    protected function order(): Order {
        $order = Order::noOrder();
        if ($this->Q_order) {
            $orderSchemeName = $this->Q_order;
            $signatures = array_filter(static::orderSignatures(), function (Signature $signature) use($orderSchemeName) {
                return $signature->name == $orderSchemeName;
            });
            $signature = array_pop($signatures);
            /** @var OrderScheme $orderScheme */
            $orderScheme = $signature->declaration;
            foreach ($orderScheme->fields as $field) {
                $order->add(CF::_($field[OrderScheme::field]->fullName), $field[OrderScheme::direction]);
            }
        }
        return $order;
    }

    protected function pager(): Pager {
        return Pager::unlimited();
    }

    protected static function association(): ?Signature
    {
        return null;
    }
}