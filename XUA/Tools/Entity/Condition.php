<?php


namespace XUA\Tools\Entity;



use Exception;
use ReflectionClass;
use ReflectionException;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\Sequence;
use XUA\Exceptions\EntityConditionException;
use XUA\Exceptions\InstantiationException;
use XUA\Exceptions\SuperValidationException;

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

    private array $joins = [];

    public string $template = "";
    public array $parameters = [];

    /**
     * @throws InstantiationException
     */
    public function __construct()
    {
        throw new InstantiationException('cannot instantiate class `Condition` directly, use `leaf`, `falseLeaf`, or `trueLeaf` methods.');
    }

    public static function relations() : array
    {
        return (new ReflectionClass(self::class))->getConstants();
    }

    /**
     * @throws ReflectionException
     * @throws SuperValidationException
     * @throws EntityConditionException
     */
    public static function leaf (ConditionField $field, string $relation, mixed $value = null) : Condition
    {
        if (!in_array($relation, self::relations())) {
            throw new EntityConditionException('Invalid relation provided. Relation must be a constant of class Condition.');
        }

        if (is_a($field->signature->type, EntityRelation::class)) {
            throw (new EntityConditionException)->setError($field->signature->name, 'Cannot filter on relational field itself. Use relIf or relMust function on the CF.');
        }

        /** @var Condition $condition */
        $condition = (new ReflectionClass(Condition::class))->newInstanceWithoutConstructor();

        $condition->template = str_replace('$', $field->name(), $relation);
        $condition->joins = $field->joins();

        $fieldType = $field->signature->type;

        if ($relation == self::BETWEEN or $relation == self::NBETWEEN) {
            if ((new Sequence(['minLength' => 2, 'maxLength' => 2]))->accepts($value, $message)) {
                throw new EntityConditionException('When using BETWEEN or NBETWEEN, the provided value must be an array of length 2.' . PHP_EOL . $message);
            }
            $condition->parameters = [$fieldType->marshalDatabase($value[0]), $fieldType->marshalDatabase($value[1])];
        } elseif ($relation == self::IN or $relation == self::NIN) {
            if ((new Sequence([]))->accepts($value, $message)) {
                throw new EntityConditionException('When using IN or NIN, the provided value must be an array.' . PHP_EOL . $message);
            }
            $condition->parameters = [$fieldType->marshalDatabase($value)];
        } elseif ($relation == self::ISNULL or $relation == self::NISNULL) {
            if ($value !== null) {
                throw new EntityConditionException('When using ISNULL or NISNULL, the provided value must be null.');
            }
            $condition->parameters = [];
        } else {
            $condition->parameters = [$fieldType->marshalDatabase($value)];
        }

        return $condition;
    }

    public static function trueLeaf() : Condition
    {
        /** @var Condition $condition */
        $condition = (new ReflectionClass(Condition::class))->newInstanceWithoutConstructor();
        $condition->template = 'TRUE';
        return $condition;
    }

    public static function falseLeaf() : Condition
    {
        /** @var Condition $condition */
        $condition = (new ReflectionClass(Condition::class))->newInstanceWithoutConstructor();
        $condition->template = 'FALSE';
        return $condition;
    }

    public function and(ConditionField $field, string $relation, mixed $value = null) : Condition
    {
        return $this->andC(Condition::leaf($field, $relation, $value));
    }

    public function or(ConditionField $field, string $relation, mixed $value = null) : Condition
    {
        return $this->orC(Condition::leaf($field, $relation, $value));
    }

    public function xor(ConditionField $field, string $relation, mixed $value = null) : Condition
    {
        return $this->xorC(Condition::leaf($field, $relation, $value));
    }

    public function not() : Condition
    {
        $this->template = "NOT ($this->template)";
        return $this;
    }

    public function andC(Condition $condition) : Condition
    {
        $this->template = "($this->template) AND ($condition->template)";
        $this->parameters = array_merge($this->parameters, $condition->parameters);
        $this->joins = array_merge($this->joins, $condition->joins);
        return $this;
    }

    public function orC(Condition $condition) : Condition
    {
        $this->template = "($this->template) OR ($condition->template)";
        $this->parameters = array_merge($this->parameters, $condition->parameters);
        $this->joins = array_merge($this->joins, $condition->joins);
        return $this;
    }

    public function xorC(Condition $condition) : Condition
    {
        $this->template = "($this->template) XOR ($condition->template)";
        $this->parameters = array_merge($this->parameters, $condition->parameters);
        $this->joins = array_merge($this->joins, $condition->joins);
        return $this;
    }

    public static function _and_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->andC($rightCondition);
    }

    public static function _or_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->orC($rightCondition);
    }

    public static function _xor_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->xorC($rightCondition);
    }

    public static function _not_(Condition $condition) : Condition
    {
        return $condition->not();
    }

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

    public function render(): string
    {
        return QueryBinder::bind($this->template, $this->parameters);
    }
}