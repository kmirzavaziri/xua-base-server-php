<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Exceptions\EntityConditionException;

final class Condition
{
    const GRATER      = "$ > ?";
    const NGRATER     = "$ <= ?";
    const GRATEREQ    = "$ >= ?";
    const NGRATEREQ   = "$ < ?";
    const LESS        = "$ < ?";
    const NLESS       = "$ >= ?";
    const LESSEQ      = "$ <= ?";
    const NLESSEQ     = "$ > ?";
    const EQ          = "$ = ?";
    const NEQ         = "$ != ?";
    const NULLSAFEEQ  = "$ <=> ?";
    const NNULLSAFEEQ = "NOT ($ <=> ?)";
    const BETWEEN     = "$ BETWEEN ? AND ?";
    const NBETWEEN    = "$ NOT BETWEEN ? AND ?";
    const IN          = "$ IN (?)";
    const NIN         = "$ NOT IN (?)";
    const IS          = "$ IS ?";
    const NIS         = "$ IS NOT ?";
    const ISNULL      = "$ IS NULL";
    const NISNULL     = "$ IS NOT NULL";
    const LIKE        = "$ LIKE ?";
    const NOT_LIKE    = "$ NOT LIKE ?";
    const REGEXP      = "$ REGEXP ?";
    const NOT_REGEXP  = "$ NOT REGEXP ?";
    const HAS         = "FIND_IN_SET(?, $) > 0";
    const RELATION_ = [
        self::GRATER,
        self::NGRATER,
        self::GRATEREQ,
        self::NGRATEREQ,
        self::LESS,
        self::NLESS,
        self::LESSEQ,
        self::NLESSEQ,
        self::EQ,
        self::NEQ,
        self::NULLSAFEEQ,
        self::NNULLSAFEEQ,
        self::BETWEEN,
        self::NBETWEEN,
        self::IN,
        self::NIN,
        self::IS,
        self::NIS,
        self::ISNULL,
        self::NISNULL,
        self::LIKE,
        self::NOT_LIKE,
        self::REGEXP,
        self::NOT_REGEXP,
        self::HAS,
    ];

    /**
     * @var string
     */
    public string $template = "";
    /**
     * @var array
     */
    public array $parameters = [];

    /**
     * @var Join[]
     */
    private array $joins = [];
    /**
     * @var CF[]
     */
    private array $columns = [];

    /**
     */
    private function __construct() {}

    /**
     * @param CF $field
     * @param string $relation
     * @param mixed|null $value
     * @return Condition
     */
    public static function leaf(CF $field, string $relation, mixed $value = null, $marshal = true): Condition
    {
        if (!in_array($relation, self::RELATION_)) {
            throw new EntityConditionException('Invalid relation provided. Relation must be a constant of class Condition.');
        }

        if (is_a($field->signature->declaration, EntityRelation::class)) {
            $field->rel($field->signature->declaration->relatedEntity::id);
        }

        $condition = new Condition();

        $condition->template = str_replace('$', $field->name(), $relation);
        $condition->joins = $field->joins();

        self::validate($field, $relation, $value);

        $condition->parameters = self::marshalDatabase($field, $relation, $value, $marshal);

        $condition->columns = [$field];

        return $condition;
    }

    private static function validate(CF $field, string $relation, mixed $value): void
    {
        $fieldType = $field->signature->declaration;

        if (in_array($relation, [Condition::BETWEEN, Condition::NBETWEEN])) {
            $fieldTypeArray = new Sequence([Sequence::minLength => 2, Sequence::maxLength => 2, Sequence::type => $fieldType]);
            if ($fieldTypeArray->explicitlyAccepts($value, $checkMessage)) {
                // @TODO from dict
                // @TODO exp
                throw new EntityConditionException('When using BETWEEN or NBETWEEN, the provided value must be an array of length 2.' . PHP_EOL . $checkMessage);
            }
        } elseif (in_array($relation, [Condition::IN, Condition::NIN])) {
            $fieldTypeArray = new Sequence([Sequence::type => $fieldType]);
            if (!$fieldTypeArray->explicitlyAccepts($value, $checkMessage)) {
                // @TODO exp
                throw new EntityConditionException('When using IN or NIN, the provided value must be an array.' . PHP_EOL . $checkMessage);
            }
        } elseif (in_array($relation, [Condition::ISNULL, Condition::NISNULL])) {
            if ($value !== null) {
                // @TODO from dict
                // @TODO exp
                throw new EntityConditionException('When using ISNULL or NISNULL, the provided value must be null.');
            }
        } elseif (in_array($relation, [
            Condition::GRATER, Condition::NGRATER, Condition::GRATEREQ, Condition::NGRATEREQ,
            Condition::LESS, Condition::NLESS, Condition::LESSEQ, Condition::NLESSEQ,
            Condition::EQ, Condition::NEQ,
            Condition::NULLSAFEEQ, Condition::NNULLSAFEEQ,
        ])) {
            if (!$fieldType->explicitlyAccepts($value, $checkMessage)) {
                throw new EntityConditionException($checkMessage);
            }
        }
    }

    private static function marshalDatabase(CF $field, string $relation, mixed $value, $marshal = true): mixed
    {
        $fieldType = $field->signature->declaration;

        if (in_array($relation, [Condition::BETWEEN, Condition::NBETWEEN])) {
            return [
                is_string($value[0]) and str_contains($value[0], '$FILTER')
                    ? $value[0]
                    : ($marshal ? $fieldType->marshalDatabase($value[0]) : $value[0]),
                $marshal ? $fieldType->marshalDatabase($value[1]) : $value[1],
            ];
        }

        if (in_array($relation, [Condition::ISNULL, Condition::NISNULL])) {
            return [];
        }

        if ($marshal and in_array($relation, [
            Condition::GRATER, Condition::NGRATER, Condition::GRATEREQ, Condition::NGRATEREQ,
            Condition::LESS, Condition::NLESS, Condition::LESSEQ, Condition::NLESSEQ,
            Condition::EQ, Condition::NEQ,
            Condition::NULLSAFEEQ, Condition::NNULLSAFEEQ,
        ])) {
            return [$fieldType->marshalDatabase($value)];
        }

        return [$value];
    }

    /**
     * @param string $template
     * @param array $parameters
     * @param Join[] $joins
     * @return Condition
     */
    public static function rawLeaf(string $template, array $parameters = [], array $joins = []) : Condition
    {
        $condition = new Condition();
        $condition->template = $template;
        $condition->parameters = $parameters;
        $condition->joins = $joins;
        return $condition;
    }

    /**
     * @return Condition
     */
    public static function trueLeaf() : Condition
    {
        return self::rawLeaf('TRUE');
    }

    /**
     * @return Condition
     */
    public static function falseLeaf() : Condition
    {
        return self::rawLeaf('FALSE');
    }

    /**
     * @param CF $field
     * @param string $relation
     * @param mixed|null $value
     * @return $this
     */
    public function and(CF $field, string $relation, mixed $value = null) : Condition
    {
        return $this->andC(Condition::leaf($field, $relation, $value));
    }

    /**
     * @param CF $field
     * @param string $relation
     * @param mixed|null $value
     * @return $this
     */
    public function or(CF $field, string $relation, mixed $value = null) : Condition
    {
        return $this->orC(Condition::leaf($field, $relation, $value));
    }

    /**
     * @param CF $field
     * @param string $relation
     * @param mixed|null $value
     * @return $this
     */
    public function xor(CF $field, string $relation, mixed $value = null) : Condition
    {
        return $this->xorC(Condition::leaf($field, $relation, $value));
    }

    /**
     * @param string $template
     * @param array $parameters
     * @param array $joins
     * @return $this
     */
    public function andR(string $template, array $parameters = [], array $joins = []) : Condition
    {
        return $this->andC(Condition::rawLeaf($template, $parameters, $joins));
    }

    /**
     * @param string $template
     * @param array $parameters
     * @param array $joins
     * @return $this
     */
    public function orR(string $template, array $parameters = [], array $joins = []) : Condition
    {
        return $this->orC(Condition::rawLeaf($template, $parameters, $joins));
    }

    /**
     * @param string $template
     * @param array $parameters
     * @param array $joins
     * @return $this
     */
    public function xorR(string $template, array $parameters = [], array $joins = []) : Condition
    {
        return $this->xorC(Condition::rawLeaf($template, $parameters, $joins));
    }

    /**
     * @return $this
     */
    public function not() : Condition
    {
        $this->template = "NOT ($this->template)";
        return $this;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function andC(Condition $condition) : Condition
    {
        $this->template = "($this->template) AND ($condition->template)";
        $this->parameters = array_merge($this->parameters, $condition->parameters);
        $this->joins = array_merge($this->joins, $condition->joins);
        $this->columns = array_merge($this->columns, $condition->columns);
        return $this;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function orC(Condition $condition) : Condition
    {
        $this->template = "($this->template) OR ($condition->template)";
        $this->parameters = array_merge($this->parameters, $condition->parameters);
        $this->joins = array_merge($this->joins, $condition->joins);
        $this->columns = array_merge($this->columns, $condition->columns);
        return $this;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function xorC(Condition $condition) : Condition
    {
        $this->template = "($this->template) XOR ($condition->template)";
        $this->parameters = array_merge($this->parameters, $condition->parameters);
        $this->joins = array_merge($this->joins, $condition->joins);
        $this->columns = array_merge($this->columns, $condition->columns);
        return $this;
    }

    /**
     * @param Condition $leftCondition
     * @param Condition $rightCondition
     * @return Condition
     */
    public static function _and_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->andC($rightCondition);
    }

    /**
     * @param Condition $leftCondition
     * @param Condition $rightCondition
     * @return Condition
     */
    public static function _or_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->orC($rightCondition);
    }

    /**
     * @param Condition $leftCondition
     * @param Condition $rightCondition
     * @return Condition
     */
    public static function _xor_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->xorC($rightCondition);
    }

    /**
     * @param Condition $condition
     * @return Condition
     */
    public static function _not_(Condition $condition) : Condition
    {
        return $condition->not();
    }

    /**
     * @return string
     */
    public function joins(): string
    {
        $joins = [];
        foreach ($this->joins as $join) {
            in_array($join, $joins) or $joins[] = $join;
        }
        return implode(PHP_EOL, array_map(function (Join $join) {
            return $join->expression();
        }, $joins));
    }

    public function columnsExpression(string $existingColumnsExpression): string
    {
        $existingColumnExpressions = explode(',', $existingColumnsExpression);
        $existingColumnExpressionsDict = [];
        foreach ($existingColumnExpressions as $columnExpression) {
            $existingColumnExpressionsDict[trim($columnExpression)] = true;
        }
        $result = [];
        foreach ($this->columns as $field) {
            if ($field->signature->declaration->databaseType() != 'DONT STORE') {
                $name = $field->name();
                if (!isset($existingColumnExpressionsDict[$name])) {
                    $result[] = $name;
                }
            }
        }
        return implode(', ', $result);
    }
}