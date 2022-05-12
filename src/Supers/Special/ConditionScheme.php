<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Eves\Super;
use Xua\Core\Supers\Highers\Instance;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Tools\Entity\CF;
use Xua\Core\Tools\Entity\Condition;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property \Xua\Core\Tools\Entity\CF field
 * @property string relation
 */
class ConditionScheme extends Super
{
    const field = self::class . '::field';
    const relation = self::class . '::relation';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::field, true, null,
                new Instance([
                    Instance::of => CF::class,
                    Instance::strict => true,
                    Instance::acceptObject => true,
                ])
            ),
            Signature::new(false, static::relation, false, false, new Enum([
                Enum::nullable => false,
                Enum::values => Condition::RELATION_,
            ])),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        $fieldType = $this->field->signature->declaration;

        if (in_array($this->relation, [Condition::BETWEEN, Condition::NBETWEEN])) {
            $fieldTypeArray = new Sequence([Sequence::minLength => 2, Sequence::maxLength => 2, Sequence::type => $fieldType]);
            if ($fieldTypeArray->explicitlyAccepts($input, $checkMessage)) {
                // @TODO from dict
                $message = 'When using BETWEEN or NBETWEEN, the provided value must be an array of length 2.' . PHP_EOL . $checkMessage;
                return false;
            }
        } elseif (in_array($this->relation, [Condition::IN, Condition::NIN])) {
            $fieldTypeArray = new Sequence([Sequence::type => $fieldType]);
            if (!$fieldTypeArray->explicitlyAccepts($input, $checkMessage)) {
                // @TODO from dict
                $message = 'When using IN or NIN, the provided value must be an array.' . PHP_EOL . $checkMessage;
                return false;
            }
        } elseif (in_array($this->relation, [Condition::ISNULL, Condition::NISNULL])) {
            if ($input !== null) {
                // @TODO from dict
                $message = 'When using ISNULL or NISNULL, the provided value must be null.';
                return false;
            }
        } else {
            if (!$fieldType->explicitlyAccepts($input, $checkMessage)) {
                $message = $checkMessage;
                return false;
            }
        }
        return true;
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        $fieldType = $this->field->signature->declaration;

        if (in_array($this->relation, [Condition::BETWEEN, Condition::NBETWEEN])) {
            return [$fieldType->marshalDatabase($input[0]), $fieldType->marshalDatabase($input[1])];
        }

        if (in_array($this->relation, [Condition::ISNULL, Condition::NISNULL])) {
            return [];
        }

        if (in_array($this->relation, [
            Condition::GRATER, Condition::NGRATER, Condition::GRATEREQ, Condition::NGRATEREQ,
            Condition::LESS, Condition::NLESS, Condition::LESSEQ, Condition::NLESSEQ,
            Condition::EQ, Condition::NEQ,
            Condition::NULLSAFEEQ, Condition::NNULLSAFEEQ,
        ])) {
            return [$fieldType->marshalDatabase($input)];
        }

        return [$input];
    }

    protected function _marshal(mixed $input): mixed
    {
        return $this->_marshalDatabase($input);
    }

    protected function _unmarshalDatabase(mixed $input): mixed
    {
        $fieldType = $this->field->signature->declaration;

        if (in_array($this->relation, [Condition::BETWEEN, Condition::NBETWEEN])) {
            return [$fieldType->unmarshalDatabase($input[0]), $fieldType->unmarshalDatabase($input[1])];
        }

        if (in_array($this->relation, [
            Condition::GRATER, Condition::NGRATER, Condition::GRATEREQ, Condition::NGRATEREQ,
            Condition::LESS, Condition::NLESS, Condition::LESSEQ, Condition::NLESSEQ,
            Condition::EQ, Condition::NEQ,
            Condition::NULLSAFEEQ, Condition::NNULLSAFEEQ,
        ])) {
            return $fieldType->unmarshalDatabase($input);
        }

        return $input;
    }

    protected function _unmarshal(mixed $input): mixed
    {
        return $this->_unmarshalDatabase($input);
    }

    protected function _databaseType(): ?string
    {
        return 'DONT STORE';
    }

    protected function _phpType(): string
    {
        return 'null';
    }
}