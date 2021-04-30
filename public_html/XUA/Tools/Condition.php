<?php


namespace XUA\Tools;



use ReflectionClass;
use Supers\Basics\Highers\Sequence;
use XUA\Exceptions\EntityConditionException;

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

    public string $template = "";
    public array $parameters = [];

    public static function relations() : array
    {
        return (new ReflectionClass(self::class))->getConstants();
    }

    public static function leaf (EntityFieldSignature $field, string $relation, mixed $value) : Condition
    {
        if (!in_array($relation, self::relations())) {
            throw new EntityConditionException('Invalid relation provided. Relation must be a constant of class Condition.');
        }

        $condition = new Condition();

        $condition->template = str_replace('$', $field->name, $relation);

        if ($relation == self::BETWEEN or $relation == self::NBETWEEN) {
            if ((new Sequence(['minLength' => 2, 'maxLength' => 2]))->accepts($value, $message)) {
                throw new EntityConditionException('When using BETWEEN or NBETWEEN, the provided value must be an array of length 2.' . PHP_EOL . $message);
            }
            $condition->parameters = [$value[0], $value[1]];
        } elseif ($relation == self::IN or $relation == self::NIN) {
            if ((new Sequence([]))->accepts($value, $message)) {
                throw new EntityConditionException('When using IN or NIN, the provided value must be an array.' . PHP_EOL . $message);
            }
            $condition->parameters[] = $value;
        } elseif ($relation == self::ISNULL or $relation == self::NISNULL) {
            if ($value !== null) {
                throw new EntityConditionException('When using ISNULL or NISNULL, the provided value must be null.');
            }
        } else {
            $condition->parameters[] = $value;
        }

        return $condition;
    }

    public static function trueLeaf() : Condition
    {
        $condition = new Condition();
        $condition->template = 'TRUE';
        return $condition;
    }

    public static function falseLeaf() : Condition
    {
        $condition = new Condition();
        $condition->template = 'FALSE';
        return $condition;
    }

    public function and(Condition $condition) : Condition
    {
        $this->template = "($this->template) AND ($condition->template)";
        $this->parameters += $condition->parameters;
        return $this;
    }

    public function or(Condition $condition) : Condition
    {
        $this->template = "($this->template) OR ($condition->template)";
        $this->parameters += $condition->parameters;
        return $this;
    }

    public function xor(Condition $condition) : Condition
    {
        $this->template = "($this->template) XOR ($condition->template)";
        $this->parameters += $condition->parameters;
        return $this;
    }

    public function not() : Condition
    {
        $this->template = "NOT ($this->template)";
        return $this;
    }

    public static function _and_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->and($rightCondition);
    }

    public static function _or_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->or($rightCondition);
    }

    public static function _xor_(Condition $leftCondition, Condition $rightCondition) : Condition
    {
        return $leftCondition->xor($rightCondition);
    }

    public static function _not_(Condition $condition) : Condition
    {
        return $condition->not();
    }


}