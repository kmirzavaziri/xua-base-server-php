<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Exceptions\SuperMarshalException;
use Xua\Core\Supers\Special\ConditionScheme;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Exceptions\EntityConditionException;
use Xua\Core\Exceptions\SuperValidationException;

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
     */
    private function __construct() {}

    /**
     * @param CF $field
     * @param string $relation
     * @param mixed|null $value
     * @return Condition
     */
    public static function leaf(CF $field, string $relation, mixed $value = null): Condition
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

        $conditionScheme = new ConditionScheme([
            ConditionScheme::field => $field,
            ConditionScheme::relation => $relation,
        ]);

        if (!$conditionScheme->accepts($value, $message)) {
            throw (new EntityConditionException())->fromErrors($message);
        }

        $condition->parameters = $conditionScheme->marshalDatabase($value);

        return $condition;
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
}